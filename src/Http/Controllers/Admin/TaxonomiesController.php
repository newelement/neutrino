<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\ObjectTerm;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Traits\CustomFields;
use Newelement\Neutrino\Models\ActivityLog;

class TaxonomiesController extends Controller
{
	use CustomFields;

	public function indexTypes()
	{
		$taxonomies = TaxonomyType::orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
		$edit_taxonomy = new \stdClass();
		$edit_taxonomy->title = '';
		$edit_taxonomy->slug = '';
		$edit_taxonomy->description = '';
        $edit_taxonomy->meta_description = '';
        $edit_taxonomy->social_description = '';
        $edit_taxonomy->keywords = '';
        $edit_taxonomy->social_image_1 = '';
        $edit_taxonomy->social_image_2 = '';
		$edit_taxonomy->hierarchical = 1;
		$edit_taxonomy->taxonomy_image = '';
        $edit_taxonomy->featuredImage = false;
		$edit_taxonomy->show_on = collect([]);

		$entryTypes = EntryType::orderBy('entry_type', 'asc')->get();

		return view('neutrino::admin.taxonomies.index-types', [
				'taxonomies' => $taxonomies,
				'edit_taxonomy' => $edit_taxonomy,
				'entry_types' => $entryTypes,
				'edit' => false]);
	}

	public function index($id)
	{
		$taxonomies = Taxonomy::where('taxonomy_type_id', $id)->orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
		$taxes = Taxonomy::where('taxonomy_type_id', $id)->orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
		$taxonomy = TaxonomyType::where('id', $id)->first();
		$edit_taxonomy = new \stdClass();
		$edit_taxonomy->title = '';
		$edit_taxonomy->slug = '';
		$edit_taxonomy->description = '';
        $edit_taxonomy->meta_description = '';
        $edit_taxonomy->social_description = '';
        $edit_taxonomy->keywords = '';
        $edit_taxonomy->social_image_1 = '';
        $edit_taxonomy->social_image_2 = '';
		$edit_taxonomy->taxonomy_image = '';
		$edit_taxonomy->featuredImage = false;

		$fieldGroups = $this->getFieldGroups('taxonomy', $taxonomy->slug);

		return view('neutrino::admin.taxonomies.index', [
		        'taxonomies' => $taxonomies,
		        'taxonomy' => $taxonomy,
		        'taxes' => $taxes,
		        'edit_taxonomy' => $edit_taxonomy,
		        'field_groups' => $fieldGroups,
		        'edit' => false]);
	}

	public function create(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

		$type = TaxonomyType::find($id);

		$tax = new Taxonomy;
		$tax->title = $request->title;
		$tax->slug = toSlug($request->title, 'taxonomy');
		$tax->description = $request->description;
        $tax->meta_description = $request->meta_description;
        $tax->social_description = $request->social_description;
        $tax->keywords = $request->keywords;
        $tax->social_image_1 = $request->social_image_1;
        $tax->social_image_2 = $request->social_image_2;
		$tax->taxonomy_type_id = $id;
		$tax->taxonomy_image = $request->taxonomy_image;
		$tax->parent_id = $request->parent_id;
		$tax->save();

		if( $request->featured_image ){
			$path = $request->featured_image;
			$media = new ObjectMedia;
			$media->object_id = $tax->id;
			$media->object_type = 'taxonomy_type';
			$media->featured = 1;
			$media->file_path = parse_url($path, PHP_URL_PATH);
			$media->save();
		}

		// Custom Fields
		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $tax->id, 'taxonomy');
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomy.create',
            'object_type' => 'taxonomy',
            'object_id' => $tax->id,
            'content' => 'Taxonomy created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomies/'.$id)->with('success', $type->title.' created.');
	}

	public function createType(Request $request)
	{

		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

		$tax = new TaxonomyType;
		$tax->title = $request->title;
		$tax->slug = toSlug($request->title, 'taxonomy_type');
		$tax->description = $request->description;
        $tax->meta_description = $request->meta_description;
        $tax->social_description = $request->social_description;
        $tax->keywords = $request->keywords;
        $tax->social_image_1 = $request->social_image_1;
        $tax->social_image_2 = $request->social_image_2;
		$tax->hierarchical = $request->hierarchical? $request->hierarchical : 0;
		$tax->show_on = implode(',',$request->show_on);
		$tax->save();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomytype.create',
            'object_type' => 'taxonomytype',
            'object_id' => $tax->id,
            'content' => 'Taxonomy Type created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomy-types')->with('success', 'Taxonomy created.');
	}

	public function getEditType($id)
	{
		$tax = TaxonomyType::find($id);
		$tax->show_on = collect(explode(',',$tax->show_on));
		$taxonomies = TaxonomyType::orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->get();

		return view('neutrino::admin.taxonomies.index-types', [
		                'taxonomies' => $taxonomies,
		                'edit_taxonomy' => $tax,
		                'entry_types' => $entryTypes,
		                'edit' => true]);
	}

	public function getEdit($type, $id)
	{
		$tax = Taxonomy::find($id);
		$taxes = Taxonomy::where('taxonomy_type_id', $type)->where('id', '<>', $tax->id)->orderBy('title', 'asc')->get();
		$taxonomy = TaxonomyType::where('id', $type)->first();
		$taxonomies = Taxonomy::orderBy('sort', 'asc')->orderBy('title', 'asc')->get();

		$fieldGroups = $this->getFieldGroups('taxonomy', $taxonomy->slug);

		return view('neutrino::admin.taxonomies.index', [
		            'taxonomies' => $taxonomies,
		            'taxonomy' => $taxonomy,
		            'edit_taxonomy' => $tax,
		            'taxes' => $taxes,
		            'field_groups' => $fieldGroups,
		            'edit' => true]);
	}

	public function updateType(Request $request, $id)
	{
		$taxonomies = TaxonomyType::orderBy('title', 'asc')->paginate(15);
		$tax = TaxonomyType::find($id);
		$tax->title = $request->title;
		$tax->description = $request->description;
        $tax->meta_description = $request->meta_description;
        $tax->social_description = $request->social_description;
        $tax->keywords = $request->keywords;
        $tax->social_image_1 = $request->social_image_1;
        $tax->social_image_2 = $request->social_image_2;
		$tax->hierarchical = $request->hierarchical? $request->hierarchical : 0;
		$tax->show_on = is_array($request->show_on)? implode(',', $request->show_on) : '';
		$tax->save();

        if( $request->featured_image ){
            $path = $request->featured_image;
            ObjectMedia::updateOrCreate([
                'object_id' => $tax->id,
                'object_type' => 'taxonomy_type',
                'featured' => 1
            ], [ 'file_path' => parse_url($path, PHP_URL_PATH) ]);
        } else {
            ObjectMedia::where([
                'object_id' => $tax->id,
                'object_type' => 'taxonomy_type',
                'featured' => 1
                ])->delete();
        }

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomytype.update',
            'object_type' => 'taxonomytype',
            'object_id' => $tax->id,
            'content' => 'Taxonomy type updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomy-types')->with('success', 'Taxonomy updated.');
	}

	public function update(Request $request, $type, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
	    ]);

		$taxType = TaxonomyType::find($type);

		$tax = Taxonomy::find($id);
		$tax->title = $request->title;
		//$tax->slug = toSlug($request->title, 'taxonomy');
		$tax->description = $request->description;
        $tax->meta_description = $request->meta_description;
        $tax->social_description = $request->social_description;
        $tax->keywords = $request->keywords;
        $tax->social_image_1 = $request->social_image_1;
        $tax->social_image_2 = $request->social_image_2;
		$tax->taxonomy_type_id = $type;
		$tax->parent_id = $request->parent_id? $request->parent_id : 0;
		$tax->save();

		if( $request->featured_image ){
			$path = $request->featured_image;
			ObjectMedia::updateOrCreate([
				'object_id' => $tax->id,
				'object_type' => 'taxonomy',
				'featured' => 1
			], [ 'file_path' => parse_url($path, PHP_URL_PATH) ]);
		} else {
			ObjectMedia::where([
				'object_id' => $tax->id,
				'object_type' => 'taxonomy',
				'featured' => 1
				])->delete();
		}

		// Custom Fields
		$customFields = $request->cfs;
		if( $customFields ){
			$this->parseCustomFields($customFields, $tax->id, 'taxonomy');
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomy.updated',
            'object_type' => 'taxonomy',
            'object_id' => $tax->id,
            'content' => 'Taxonomy updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomies/'.$taxType->id)->with('success', $taxType->title.' updated.');
	}

	public function deleteType($id)
	{
		$type = TaxonomyType::find($id);
		if( $type->slug === 'category' ){
			return redirect('/admin/taxonomy-types')->with('error', 'Cannot delete category type.');
		}

		$terms = Taxonomy::where('taxonomy_type_id', $type->id)->get();
		foreach( $terms as $term ){
			ObjectTerm::where('taxonomy_id', $term->id)->delete();
			Taxonomy::where('taxonomy_id', $term->id)->delete();
		}

        $type->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomytype.delete',
            'object_type' => 'taxonomytype',
            'object_id' => $id,
            'content' => 'Taxonomy type deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomy-types')->with('success', 'Taxonomy deleted.');
	}

	public function delete($type, $id)
	{
		$type = TaxonomyType::find($type);
		$term = Taxonomy::find($id);
		Taxonomy::where('parent_id', $term->id)->update(['parent_id' => 0]);
		ObjectTerm::where('taxonomy_id', $term->id)->delete();
		$title = $term->title;
		$term->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'taxonomy.delete',
            'object_type' => 'taxonomy',
            'object_id' => $id,
            'content' => 'Taxonomy deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/taxonomies/'.$type->id)->with('success', $title.' deleted.');
	}

    public function sortTerms(Request $request)
    {
        $terms = $request->items;

        foreach( $terms as $key => $id ){
            $id = (int) $id;
            $tax = Taxonomy::find($id);
            $tax->sort = $key;
            $tax->save();
        }
    }

    public function sortTaxonomy(Request $request)
    {
        $taxonomies = $request->items;

        foreach( $taxonomies as $key => $id ){
            $id = (int) $id;
            $tax = TaxonomyType::find($id);
            $tax->sort = $key;
            $tax->save();
        }
    }

}
