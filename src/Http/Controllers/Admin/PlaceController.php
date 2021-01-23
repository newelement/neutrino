<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\Place;
use Newelement\Neutrino\Models\ActivityLog;

class PlaceController extends Controller
{

	public function __construct(){}

	public function index()
	{
		$places = Place::paginate(30);

		return view('neutrino::admin.places.index', ['places' => $places]);
	}

	public function getCreate()
	{
		return view('neutrino::admin.places.create');
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'place_name' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$place = new Place;
		$place->location_name = $request->place_name;
		$place->slug = toSlug($request->slug, 'place');
		$place->description = htmlentities($request->description);
		$place->address = $request->address;
		$place->address2 = $request->address2;
		$place->city = $request->city;
		$place->state = $request->state;
		$place->postal = $request->zip;
		$place->phone = $request->phone;
		$place->email = $request->email;
		$place->url = $request->url;
		$place->country = $request->country;
		$place->lat = $request->lat;
		$place->lon = $request->lon;
		$place->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $place->id,
				'object_type' => 'place',
				'featured' => 1
			], [ 'file_path' => $request->featured_image ]);

		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'place.create',
            'object_type' => 'place',
            'object_id' => $place->id,
            'content' => 'Place created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/places/'.$place->id)->with('success', 'Place created.');

	}

	public function get($id)
	{
		$place = Place::find($id);
		$place->featuredImage = ObjectMedia::where([ 'object_id' => $id, 'featured' => 1, 'object_type' => 'place' ])->first();
		return view('neutrino::admin.places.edit', ['place' => $place]);
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'place_name' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$place = Place::find($id);
		$place->location_name = $request->place_name;
		$place->description = htmlentities($request->description);
		$place->address = $request->address;
		$place->address2 = $request->address2;
		$place->city = $request->city;
		$place->state = $request->state;
		$place->postal = $request->zip;
		$place->phone = $request->phone;
		$place->email = $request->email;
		$place->url = $request->url;
		$place->country = $request->country;
		$place->lat = $request->lat;
		$place->lon = $request->lon;

		if( $place->slug !== $request->slug ){
			$place->slug = toSlug($request->slug, 'place');
		}

		$place->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $id,
				'object_type' => 'place',
				'featured' => 1
			], [ 'file_path' => $request->featured_image ]);
		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'place.updated',
            'object_type' => 'place',
            'object_id' => $place->id,
            'content' => 'Place updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/places/'.$id)->with('success', 'Place updated.');

	}


	public function delete($id)
	{
		$place = Place::find($id);
		$place->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'place.deleted',
            'object_type' => 'place',
            'object_id' => $id,
            'content' => 'Place deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/places')->with('success', 'Place deleted.');
	}

}
