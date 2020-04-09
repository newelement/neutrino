<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\ObjectTerm;
use Newelement\Neutrino\Models\CfObjectData;
use Newelement\Neutrino\Traits\CustomFields;
use Newelement\Neutrino\Models\Role;
use Newelement\Neutrino\Models\Backup;
use Newelement\Neutrino\Traits\Blocks;
use Newelement\Neutrino\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PageController extends Controller
{

	use CustomFields;
    use Blocks;

	public function __construct(){}

	public function index(Request $request)
	{

        $page = $request->page? $request->page : 1;

		if( $request->s && strlen($request->s) ){
			$pages = Page::search($request->s)->sortable('title')->paginate(30);
            $pagesTotal = $pages;
		} else {
			$pagesTotal = Page::where('parent_id', 0)->sortable('title')->get();
            $pages = $this->listPages($pagesTotal)->forPage( $page, 30)->values();
		}

		$trashed = Page::onlyTrashed()->get();

		return view('neutrino::admin.pages.index', ['pages' => $pages, 'page_count'=> $pagesTotal->count(), 'page' => $page, 'trashed' => count($trashed)]);
	}

    private function listPages($pages)
    {
        $data = collect();

        foreach($pages as $page){
            $page->sub_pages = $this->listPages($page->children->sortBy('title'));
            $data->push($page);
        }

        return $data;
    }

	public function getCreate()
	{
		$fieldGroups = $this->getFieldGroups('pages');
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.pages.create', ['field_groups' => $fieldGroups, 'roles' => $roles]);
	}

    public function getSort()
    {
        $pages = Page::where('parent_id', 0)->orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
        return view( 'neutrino::admin.pages.sort', ['pages' => $pages]);
    }

    public function updateSort(Request $request)
    {
        $pages = $request->pages;
        foreach( $pages as $key => $id ){
            $id = (int) $id;
            $tax = Page::find($id);
            $tax->sort = $key;
            $tax->save();
        }
        return response()->json(['success' => true]);
    }

	public function get($id)
	{
		$page = Page::find($id);
		$page->protected = collect(explode(',',$page->protected));
		$fieldGroups = $this->getFieldGroups('pages', false, $id);
		$backups = Backup::where(['object_id' => $id, 'object_type' => 'page'])->orderBy('updated_at', 'desc')->get();
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.pages.edit' , [ 'page' => $page, 'field_groups' => $fieldGroups, 'backups' => $backups, 'roles' => $roles ]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

        if( !$request->editor_type || $request->editor_type === 'B' ){
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

		$page = Page::create([
			'title' => $request->title,
			'slug' => toSlug($request->slug, 'page'),
			'content' => $content,
            'block_content' => $block_content,
            'short_content' => htmlentities($request->short_content),
			'parent_id' => $request->parent_id? $request->parent_id : 0 ,
			'keywords' => $request->keywords ,
			'meta_description' => $request->meta_description ,
			'status' => $request->status? $request->status : 'P',
            'editor_type' => $request->editor_type? $request->editor_type : 'B',
			'social_image' => $request->social_image,
			'protected' => $request->protected ? implode(',',$request->protected) : ''
		]);

		if( $request->featured_image ){
			$path = $request->featured_image;
			$media = new ObjectMedia;
			$media->object_id = $page->id;
			$media->object_type = 'page';
			$media->featured = 1;
			$media->file_path = $path;
			$media->save();
		}

		// Custom Fields
		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $page->id, 'page');
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'page.create',
            'object_type' => 'page',
            'object_id' => $page->id,
            'content' => 'Page created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/page/'.$page->id)->with('success', 'Page created.');

	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

        $page = Page::find($id);

        if( $request->editor_type === 'B' ){

            if( $page->editor_type === 'C' ){

                $block_content = [
                    [
                        "id" => uniqid(),
                        "tag" => false,
                        "icon" => "align-left",
                        "name" => "freetext",
                        "group" => false,
                        "title" => "Text",
                        "value" => html_entity_decode($page->content),
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

            if( $page->editor_type === 'B' ){

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

		$page->title = $request->title;
		$page->slug = $page->slug === $request->slug? $request->slug : toSlug($request->slug, 'page');
		$page->content = $content;
        $page->block_content = $block_content;
        $page->short_content = htmlentities($request->short_content);
		$page->parent_id = $request->parent_id? $request->parent_id : 0 ;
		$page->keywords = $request->keywords ;
		$page->meta_description = $request->meta_description ;
		$page->status = $request->status? $request->status : 'P' ;
        $page->editor_type = $request->editor_type? $request->editor_type : 'B' ;
		$page->social_image = $request->social_image;
		$page->protected = $request->protected ? implode(',',$request->protected) : '';
		$page->save();

		// Save in cache
		$slug = $page->slug === 'home'? 'index' : $page->slug;
		Cache::forget('page_'.$slug);
        Cache::forget('block_page_'.$slug);
		if( getSetting('cache') ){
			Cache::rememberForever('page_'.$slug, function() use ($page){
				return $page;
			});
		}

		if( $request->featured_image ){
			$path = $request->featured_image;
			ObjectMedia::updateOrCreate([
				'object_id' => $page->id,
				'object_type' => 'page',
				'featured' => 1
			], [ 'file_path' => $path ]);
		} else {
			ObjectMedia::where([
				'object_id' => $page->id,
				'object_type' => 'page',
				'featured' => 1
				])->delete();
		}

		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $page->id, 'page');
		}

		if($request->ajax()){
			return response()->json(['success' => true]);
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'page.update',
            'object_type' => 'page',
            'object_id' => $page->id,
            'content' => 'Page updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/page/'.$id)->with('success', 'Page updated.');

	}

	public function getTrash()
	{
		$pages = Page::onlyTrashed()->orderBy('title', 'asc')->paginate(30);
		return view('neutrino::admin.pages.trash', [ 'pages' => $pages ]);
	}

	public function delete($id)
	{
		Page::find($id)->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'page.delete',
            'object_type' => 'page',
            'object_id' => $id,
            'content' => 'Page deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/pages')->with('success', 'Page deleted.');
	}

	public function recover($id)
	{
		Page::onlyTrashed()->where('id', $id)->restore();
		return redirect('/admin/page/'.$id)->with('success', 'Page recovered.');
	}
	public function destroy($id)
	{
		$destroyed = Page::onlyTrashed()->where('id', $id)->forceDelete();
		if($destroyed){
			ObjectTerm::where([
				'object_id' => $id,
				'object_type' => 'page'
			])->delete();
			ObjectMedia::where([
				'object_id' => $id,
				'object_type' => 'page'
			])->delete();
			CfObjectData::where([
				'object_id' => $id,
				'object_type' => 'page'
			])->delete();
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'page.destroyed',
            'object_type' => 'page',
            'object_id' => $id,
            'content' => 'Page destroyed',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/pages-trash')->with('success', 'Page destroyed.');
	}

}
