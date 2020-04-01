<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\Gallery;
use Newelement\Neutrino\Models\GalleryImage;
use Newelement\Neutrino\Models\ActivityLog;

class GalleryController extends Controller
{

	public function __construct(){}

	public function index()
	{
		$galleries = Gallery::orderBy('title', 'asc')->paginate(30);

		return view('neutrino::admin.galleries.index', ['galleries' => $galleries]);
	}

	public function getCreate()
	{
		return view('neutrino::admin.galleries.create');
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$gallery = new Gallery;
		$gallery->title = $request->title;
		$gallery->slug = toSlug($request->slug, 'gallery');
        $gallery->theme = $request->theme;
        $gallery->sort = 0;
		$gallery->description = htmlentities($request->description);
		$gallery->save();

        $images = $request->gallery_items;

        $inserts = [];
        $i = 0;
        foreach( $images as $key => $image ){
            $inserts[] = [
                'gallery_id' => $gallery->id,
                'image_path' => $images[$key]['image'],
                'title' => $images[$key]['title'],
                'caption' => $images[$key]['caption'],
                'featured' => isset($images[$key]['featured'])? 1 : 0,
                'sort' => $i,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $i++;
        }

        $inserted = GalleryImage::insert($inserts);

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'gallery.create',
            'object_type' => 'galllery',
            'object_id' => $gallery->id,
            'content' => 'Gallery created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/galleries/'.$gallery->id)->with('success', 'Gallery created.');

	}

	public function get($id)
	{
		$gallery = Gallery::find($id);
		return view('neutrino::admin.galleries.edit', ['gallery' => $gallery]);
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$gallery = Gallery::find($id);
        $gallery->title = $request->title;
        $gallery->theme = $request->theme;
        $gallery->description = htmlentities($request->description);

		if( $gallery->slug !== $request->slug ){
			$gallery->slug = toSlug($request->slug, 'gallery');
		}

		$gallery->save();

        $images = $request->gallery_items;

        $updates = [];
        $i = 0;
        foreach( $images as $key => $image ){
            $updated = GalleryImage::updateOrCreate(
                [ 'id' => $key, 'gallery_id' => $gallery->id ], [
                    'gallery_id' => $gallery->id,
                    'image_path' => $images[$key]['image'],
                    'title' => $images[$key]['title'],
                    'caption' => $images[$key]['caption'],
                    'featured' => isset($images[$key]['featured'])? 1 : 0,
                    'sort' => $i,
                    'updated_at' => now()
                ]
            );
            $i++;
        }

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'gallery.updated',
            'object_type' => 'gallery',
            'object_id' => $gallery->id,
            'content' => 'Gallery updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect()->back()->with('success', 'Gallery updated.');

	}


	public function delete($id)
	{
		$gallery = Gallery::find($id);
		$gallery->delete();

        GalleryImage::where('gallery_id', $id)->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'gallery.deleted',
            'object_type' => 'gallery',
            'object_id' => $id,
            'content' => 'Gallery deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect()->back()->with('success', 'Gallery deleted.');
	}

    public function deleteImage($id)
    {
        $gallery = GalleryImage::find($id);
        $gallery->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'gallery.image.deleted',
            'object_type' => 'galleryimage',
            'object_id' => $id,
            'content' => 'Gallery image deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

}
