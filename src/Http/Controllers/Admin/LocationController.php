<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\Place;

class LocationController extends Controller
{

	public function __construct(){}

	public function index()
	{
		$locations = Place::paginate(30);

		return view('neutrino::admin.locations.index', ['locations' => $locations]);
	}

	public function getCreate()
	{
		return view('neutrino::admin.locations.create');
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'location_name' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$location = new Place;
		$location->location_name = $request->location_name;
		$location->slug = toSlug($request->slug, 'place');
		$location->description = htmlentities($request->description);
		$location->address = $request->address;
		$location->address2 = $request->address2;
		$location->city = $request->city;
		$location->state = $request->state;
		$location->postal = $request->zip;
		$location->phone = $request->phone;
		$location->email = $request->email;
		$location->url = $request->url;
		$location->country = $request->country;
		$location->lat = $request->lat;
		$location->lon = $request->lon;
		$location->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $location->id,
				'object_type' => 'location',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);

		}

		return redirect('/admin/locations/'.$location->id)->with('success', 'Location created.');

	}

	public function get($id)
	{
		$location = Place::find($id);
		$location->featuredImage = ObjectMedia::where([ 'object_id' => $id, 'featured' => 1, 'object_type' => 'location' ])->first();
		return view('neutrino::admin.locations.edit', ['location' => $location]);
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'location_name' => 'required|max:255',
			'slug' => 'required',
	    ]);

		$location = Place::find($id);
		$location->location_name = $request->location_name;
		$location->description = htmlentities($request->description);
		$location->address = $request->address;
		$location->address2 = $request->address2;
		$location->city = $request->city;
		$location->state = $request->state;
		$location->postal = $request->zip;
		$location->phone = $request->phone;
		$location->email = $request->email;
		$location->url = $request->url;
		$location->country = $request->country;
		$location->lat = $request->lat;
		$location->lon = $request->lon;

		if( $location->slug !== $request->slug ){
			$location->slug = toSlug($request->slug, 'place');
		}

		$location->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $id,
				'object_type' => 'location',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);
		}

		return redirect('/admin/locations/'.$id)->with('success', 'Location updated.');

	}


	public function delete($id)
	{
		$location = Place::find($id);
		$location->delete();
		return redirect('/admin/locations')->with('success', 'Location deleted.');
	}

}
