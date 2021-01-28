<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Shortcode;
use Illuminate\Support\Str;
use Newelement\Neutrino\Models\ActivityLog;

class ShortcodesController extends Controller
{
	public function index()
	{
		$shortcodes = Shortcode::paginate(20);

		return view( 'neutrino::admin.shortcodes.index', [ 'shortcodes' => $shortcodes ]);
	}

	public function get($id)
	{
		$shortcode = Shortcode::findOrFail($id);

		return view( 'neutrino::admin.shortcodes.edit', [ 'shortcode' => $shortcode ]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
			'title' => 'required|max:255',
   		]);

		$shortcode = new Shortcode;
		$shortcode->title = $request->title;
    	$shortcode->slug = str_replace('-', '_', toSlug($request->title, 'shortcodes') );
		$shortcode->embed = htmlentities( $request->embed );
		$shortcode->save();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'shortcode.create',
            'object_type' => 'shortcode',
            'object_id' => $shortcode->id,
            'content' => 'Shortcode created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/shortcodes/'.$shortcode->id )->with('success', 'Shortcode created.');
	}

	public function update(Request $request, $id)
	{

		$validatedData = $request->validate([
			'title' => 'required',
   		]);

		$shortcode = Shortcode::findOrFail($id);
		$shortcode->title = $request->title;
        $shortcode->embed = htmlentities( $request->embed );
        $shortcode->save();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'shortcode.update',
            'object_type' => 'shortcodes',
            'object_id' => $shortcode->id,
            'content' => 'Shortcode updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/shortcodes/'.$id)->with('success', 'Shortcode updated.');
	}

	public function delete($id)
	{
		$setting = Shortcode::findOrFail($id);
		$setting->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'shortcode.delete',
            'object_type' => 'shortcodes',
            'object_id' => $id,
            'content' => 'Shortcode deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/shortcodes')->with('success', 'Shortcode deleted.');
	}

}
