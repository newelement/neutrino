<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\ObjectTerm;
use Newelement\Neutrino\Models\ObjectData;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\Role;
use Newelement\Neutrino\Traits\CustomFields;
use Newelement\Neutrino\Events\EntryAdded;
use Newelement\Neutrino\Events\EntryUpdated;
use Illuminate\Support\Facades\Log;
use Newelement\Neutrino\Models\Backup;
use Newelement\Neutrino\Traits\Blocks;
use Newelement\Neutrino\Models\ActivityLog;
use Carbon\Carbon;

class EntryController extends Controller
{
	use CustomFields;
    use Blocks;

	public function __construct(){}

	public function index(Request $request)
	{
		if( $request->s && strlen($request->s) ){
			$entries = Entry::where('entry_type', $request->entry_type)->search($request->s)->sortable(['created_at' => 'desc'])->paginate(30);
		} else {
			$entries = Entry::where('entry_type', $request->entry_type)->sortable(['created_at' => 'desc'])->paginate(30);
		}

		$trashed = Entry::onlyTrashed()->where('entry_type', $request->entry_type)->get();

		$entryType = EntryType::where('slug', $request->entry_type)->first();
		return view('neutrino::admin.entries.index', ['entries' => $entries, 'entry_type' => $entryType, 'trashed' => count($trashed)]);
	}

	public function getCreate()
	{
		$fieldGroups = $this->getFieldGroups('entries');
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.entries.create', ['field_groups' => $fieldGroups, 'roles' => $roles]);
	}

	public function get($id)
	{
		$entry = Entry::find($id);
		$entry->protected = collect(explode(',',$entry->protected));
		$terms = ObjectTerm::where('object_id', $entry->id)->where('object_type', 'entry')->get();
		$fieldGroups = $this->getFieldGroups('entries', $entry->entry_type, $id);
		$backups = Backup::where(['object_id' => $id, 'object_type' => $entry->entry_type])->orderBy('updated_at', 'desc')->get();
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.entries.edit' , [ 'entry' => $entry, 'terms' => $terms, 'field_groups' => $fieldGroups, 'backups' => $backups, 'roles' => $roles ]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

        if( getSetting('enable_block_editor') ){
            $blocksJSON = json_decode($request->block_content);
            $block_content = json_encode( json_decode($request->block_content, true) );
            $blocksHTML = $this->compileBlocks( $blocksJSON );
            $content = htmlentities($blocksHTML);
        } else {
            $content = htmlentities($request->content);

            $block_content = [
                [
                    "id" => uniqid(),
                    "tag" => false,
                    "icon" => "align-left",
                    "name" => "freetext",
                    "group" => false,
                    "title" => "Text",
                    "value" => $content,
                    "template" => false,
                    "field_groups" => [
                        [
                            "fields" => [],
                            "showBlockItemOptions" => false
                        ]
                    ],
                    "contentEditable" => true
                ]
            ];
            $block_content = json_encode($block_content);
        }

        if( $request->publish_date ){
            $pdate = Carbon::parse( $request->publish_date, config('neutrino.timezone') );
            $pdate->setTimezone( config('neutrino.timezone') );
            $publishDate = $pdate->setTimezone('UTC');
        } else {
            $publishDate = now();
        }

		$entry = new Entry;
		$entry->title = $request->title;
		$entry->slug = toSlug($request->slug, 'entry');
		$entry->content = $content;
        $entry->block_content = $block_content;
        $entry->short_content = htmlentities($request->short_content);
		$entry->status = $request->status? $request->status : 'P' ;
        $entry->publish_date = $publishDate;
        $entry->editor_type = $request->editor_type? $request->editor_type : 'B' ;
		$entry->keywords = $request->keywords ;
		$entry->meta_description = $request->meta_description ;
		$entry->entry_type = $request->entry_type? $request->entry_type : 'entry';
        $entry->template = $request->template? $request->template : null;
		$entry->allow_comments = $request->allow_comments? 1 : 0 ;
		$entry->social_image = $request->social_image;
        $entry->sitemap_change = $request->sitemap_change;
        $entry->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;
		$entry->protected = $request->protected? implode(',',$request->protected) : '';
		$entry->save();

		if( $entry ){

            ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entry.create',
                'object_type' => 'entry',
                'object_id' => $entry->id,
                'content' => 'Entry created',
                'log_level' => 0,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

			event(new EntryAdded($entry));
		}

		if( $request->featured_image ){
			$path = $request->featured_image;
			$media = new ObjectMedia;
			$media->object_id = $entry->id;
			$media->object_type = 'entry';
			$media->featured = 1;
			$media->file_path = $path;
			$media->save();
		}

		if($request->tax_new){
			foreach( $request->tax_new as $key => $value ){
				$tax_type_id = $key;
				if( strlen($request->tax_new[$tax_type_id]) ){

					$taxonomy = new Taxonomy;
					$taxonomy->title = $value;
					$taxonomy->slug = toSlug($value, 'taxonomy');
					$taxonomy->taxonomy_type_id = $tax_type_id;
					$taxonomy->save();

					$objTerm = new ObjectTerm;
					$objTerm->object_id = $entry->id;
					$objTerm->object_type = 'entry';
					$objTerm->taxonomy_type_id = $tax_type_id;
					$objTerm->taxonomy_id = $taxonomy->id;
					$objTerm->save();
				}
			}
		}

		if( $request->taxes ){
			foreach($request->taxes as $typeId => $terms){
				foreach($terms as $term){
					ObjectTerm::updateOrCreate(
					    ['object_id' => $entry->id, 'object_type' => 'entry'],
					    ['taxonomy_type_id' => $typeId, 'taxonomy_id' => $term]
					);
				}
			}
		}

		// Custom Fields
		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $entry->id, 'entry');
		}

        return redirect('/admin/entry/'.$entry->id.'?entry_type='.$entry->entry_type)->with('success', 'Entry created.');

	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

        $entry = Entry::find($id);

        if( $request->editor_type === 'B' ){

            if( $entry->editor_type === 'C' ){

                $block_content = [
                    [
                        "id" => uniqid(),
                        "tag" => false,
                        "icon" => "align-left",
                        "name" => "freetext",
                        "group" => false,
                        "title" => "Text",
                        "value" => html_entity_decode($entry->content),
                        "template" => false,
                        "field_groups" => [
                            [
                                "fields" => [],
                                "showBlockItemOptions" => false
                            ]
                        ],
                        "contentEditable" => true
                    ]
                ];
                $block_content = json_encode($block_content);
                $content = htmlentities($request->content);

            } else {
                $blocksJSON = json_decode($request->block_content);
                $block_content = json_encode( json_decode($request->block_content, true) );
                $blocksHTML = $this->compileBlocks( $blocksJSON );
                $content = htmlentities($blocksHTML);
            }

        } else {

            if( $entry->editor_type === 'B' ){

                $blocksJSON = json_decode($request->block_content);
                $block_content = json_encode( json_decode($request->block_content, true) );
                $blocksHTML = $this->compileBlocks( $blocksJSON );
                $content = htmlentities($blocksHTML);

            } else {
                $content = htmlentities($request->content);
                $block_content = [
                    [
                        "id" => uniqid(),
                        "tag" => false,
                        "icon" => "align-left",
                        "name" => "freetext",
                        "group" => false,
                        "title" => "Text",
                        "value" => $content,
                        "template" => false,
                        "field_groups" => [
                            [
                                "fields" => [],
                                "showBlockItemOptions" => false
                            ]
                        ],
                        "contentEditable" => true
                    ]
                ];
                $block_content = json_encode($block_content);
            }

        }

        if( $request->publish_date ){
            $pdate = Carbon::parse( $request->publish_date, config('neutrino.timezone') );
            $pdate->setTimezone( config('neutrino.timezone') );
            $publishDate = $pdate->setTimezone('UTC');
        } else {
            $publishDate = now();
        }

		$entry->title = $request->title;
		$entry->slug = $entry->slug === $request->slug? $request->slug : toSlug($request->slug, 'entry');
		$entry->content = $content;
        $entry->block_content = $block_content;
        $entry->short_content = htmlentities($request->short_content);
		$entry->status = $request->status? $request->status : 'P' ;
        $entry->publish_date = $publishDate;
        $entry->editor_type = $request->editor_type? $request->editor_type : 'B' ;
		$entry->keywords = $request->keywords ;
		$entry->meta_description = $request->meta_description ;
		$entry->entry_type = $request->entry_type;
        $entry->template = $request->template? $request->template : null;
		$entry->allow_comments = $request->allow_comments? 1 : 0 ;
		$entry->social_image = $request->social_image;
        $entry->sitemap_change = $request->sitemap_change;
        $entry->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;
		$entry->protected = $request->protected? implode(',',$request->protected) : '';
		$entry->save();

		if( $entry ){

            ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entry.updated',
                'object_type' => 'entry',
                'object_id' => $entry->id,
                'content' => 'Entry updated',
                'log_level' => 0,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

			event(new EntryUpdated($entry));
		}

		// Save in cache
		Cache::forget('entry_'.$entry->slug);
        Cache::forget('block_entry_'.$entry->slug);
		if( getSetting('cache') ){
			Cache::rememberForever('entry_'.$entry->slug, function() use($entry){
	            return $entry;
	        });
		}

		if( $request->featured_image ){
			$path = $request->featured_image;
			ObjectMedia::updateOrCreate([
				'object_id' => $entry->id,
				'object_type' => 'entry',
				'featured' => 1
			], [ 'file_path' => $path]);
		} else {
			ObjectMedia::where([
				'object_id' => $entry->id,
				'object_type' => 'entry',
				'featured' => 1
				])->delete();
		}

		if($request->tax_new){
			foreach( $request->tax_new as $key => $value ){
				$tax_type_id = $key;
				if( strlen($request->tax_new[$tax_type_id]) ){

					$taxonomy = new Taxonomy;
					$taxonomy->title = $value;
					$taxonomy->slug = toSlug($value, 'taxonomy');
					$taxonomy->taxonomy_type_id = $tax_type_id;
					$taxonomy->save();

					$objTerm = new ObjectTerm;
					$objTerm->object_id = $entry->id;
					$objTerm->object_type = 'entry';
					$objTerm->taxonomy_type_id = $tax_type_id;
					$objTerm->taxonomy_id = $taxonomy->id;
					$objTerm->save();
				}
			}
		}

		if( $request->taxes ){
			foreach($request->taxes as $typeId => $terms){
				foreach($terms as $term){
					$update = ['object_id' => $entry->id, 'object_type' => 'entry', 'taxonomy_type_id' => $typeId, 'taxonomy_id' => $term];
					$updated = ObjectTerm::updateOrCreate(
					    $update
					);
				}
			}
		}

		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $entry->id, 'entry');
		}

		if($request->ajax()){
			return response()->json(['success' => true]);
		}

		return redirect('/admin/entry/'.$id.'?entry_type='.$entry->entry_type)->with('success', 'Entry updated.');

	}

	public function indexEntryTypes()
	{
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->paginate(15);

		$edit_entry_type = new \stdClass();
		$edit_entry_type->entry_type = '';
		$edit_entry_type->slug = '';
		$edit_entry_type->label_plural = '';
		$edit_entry_type->searchable = 1;
        $edit_entry_type->featured_image = false;
        $edit_entry_type->social_image_1 = false;
        $edit_entry_type->social_image_2 = false;
        $edit_entry_type->social_description = '';
        $edit_entry_type->meta_description = '';
        $edit_entry_type->keywords = '';
        $edit_entry_type->sitemap_change = '';
        $edit_entry_type->sitemap_priority = 0.5;

		return view('neutrino::admin.entry-types.index', ['entry_types' => $entryTypes, 'edit_entry_type' => $edit_entry_type, 'edit' => false]);
	}

	public function createEntryType(Request $request)
	{
		$validatedData = $request->validate([
	        'entry_type' => 'required|max:255',
	    ]);

		$entryType = new EntryType;
		$entryType->entry_type = $request->entry_type;
		$entryType->slug = toSlug($request->entry_type, 'entry_type');
		$entryType->label_plural = $request->label_plural;
		$entryType->searchable = $request->searchable? $request->searchable : 0;
        $entryType->featured_image = $request->featured_image;
        $entryType->social_image_1 = $request->social_image_1;
        $entryType->social_image_2 = $request->social_image_2;
        $entryType->social_description = $request->social_description;
        $entryType->meta_description = $request->meta_description;
        $entryType->keywords = $request->keywords;
        $entryType->sitemap_change = $request->sitemap_change;
        $entryType->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;
		$entryType->save();

        ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entrytype.create',
                'object_type' => 'entrytype',
                'object_id' => $entryType->id,
                'content' => 'Entry type created',
                'log_level' => 0,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

		return redirect('/admin/entry-types')->with('success', 'Entry type created.');
	}

	public function getEditEntryType($id)
	{
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->paginate(15);
		$edit_entry_type = EntryType::find($id);

		return view('neutrino::admin.entry-types.index', ['entry_types' => $entryTypes, 'edit_entry_type' => $edit_entry_type, 'edit' => true]);
	}

	public function updateEntryType(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'entry_type' => 'required|max:255',
	    ]);

		$entryType = EntryType::find($id);
		$entryType->entry_type = $request->entry_type;
		$entryType->label_plural = $request->label_plural;
		$entryType->searchable = $request->searchable? $request->searchable : 0;
        $entryType->featured_image = $request->featured_image;
        $entryType->social_image_1 = $request->social_image_1;
        $entryType->social_image_2 = $request->social_image_2;
        $entryType->social_description = $request->social_description;
        $entryType->meta_description = $request->meta_description;
        $entryType->keywords = $request->keywords;
        $entryType->sitemap_change = $request->sitemap_change;
        $entryType->sitemap_priority = $request->sitemap_priority? $request->sitemap_priority : 0.5;
		$entryType->save();

        ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entrytype.updated',
                'object_type' => 'entrytype',
                'object_id' => $entryType->id,
                'content' => 'Entry type updated',
                'log_level' => 0,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

		return redirect('/admin/entry-types')->with('success', 'Entry type updated.');
	}

	public function deleteEntryType($id)
	{
		$entryType = EntryType::find($id);
		if( $entryType->slug === 'entry' ){
			return redirect('/admin/entry-types')->with('error', 'Cannot delete entry type.');
		}
		$entryType->delete();

        ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entrytype.delete',
                'object_type' => 'entrytype',
                'object_id' => $id,
                'content' => 'Entry type deleted',
                'log_level' => 1,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

		return redirect('/admin/entry-types')->with('success', 'Entry type deleted.');
	}

	public function removeEntryTerm(Request $request)
	{
		$del = ObjectTerm::where([
			'object_id' => $request->object_id,
			'object_type' => $request->object_type,
			'taxonomy_type_id' => $request->taxonomy_type_id,
			'taxonomy_id' => $request->term_id,
		])->delete();

		return response()->json(['success', $del]);
	}

	public function getTrash(Request $request)
	{
		$entries = Entry::onlyTrashed()->where('entry_type', $request->entry_type)->orderBy('title', 'asc')->paginate(30);
		return view('neutrino::admin.entries.trash', [ 'entries' => $entries ]);
	}

	public function delete(Request $request, $id)
	{
		Entry::find($id)->delete();

        ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entry.delete',
                'object_type' => 'entry',
                'object_id' => $id,
                'content' => 'Entry deleted',
                'log_level' => 1,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

		return redirect('/admin/entries?entry_type='.$request->entry_type)->with('success', ucwords(str_replace('-','',$request->entry_type)).' deleted.');
	}

	public function recover(Request $request, $id)
	{
		Entry::onlyTrashed()->where('id', $id)->where('entry_type', $request->entry_type)->restore();
		return redirect('/admin/entry/'.$id)->with('success', ucwords(str_replace('-','',$request->entry_type)).' recovered.');
	}

	public function destroy(Request $request, $id)
	{
		$destroyed = Entry::onlyTrashed()->where('id', $id)->where('entry_type', $request->entry_type)->forceDelete();
		if($destroyed){
			ObjectTerm::where([
				'object_id' => $id,
				'object_type' => 'entry'
			])->delete();
			ObjectMedia::where([
				'object_id' => $id,
				'object_type' => 'entry'
			])->delete();
			CfObjectData::where([
				'object_id' => $id,
				'object_type' => 'entry'
			])->delete();

            ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'entry.destroyed',
                'object_type' => 'entry',
                'object_id' => $id,
                'content' => 'Entry destroyed',
                'log_level' => 1,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);
		}
		return redirect('/admin/entries-trash')->with('success', ucwords(str_replace('-','',$request->entry_type)).' destroyed.');
	}

}
