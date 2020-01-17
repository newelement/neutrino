<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Setting;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
	public function index()
	{
		$settings = Setting::where('protected', 1)->orderBy('key', 'asc')->get();
		$csettings = Setting::where('protected', 0)->orderBy('key', 'asc')->get();
		$setting = new \stdClass();
		$setting->key = '';
		$setting->value = '';
		$setting->type = '';
		$setting->value_bool = 0;
		$setting->label = '';
		$setting->id = '';
		$setting->protected = 0;
		return view( 'neutrino::admin.settings.index', ['settings' => $settings, 'custom_settings' => $csettings, 'edit_setting' => $setting, 'edit' => false]);
	}

	public function get($id)
	{
		$setting = Setting::find($id);
		$settings = Setting::where('protected', 1)->orderBy('key', 'asc')->get();
		$csettings = Setting::where('protected', 0)->orderBy('key', 'asc')->get();
		return view( 'neutrino::admin.settings.index', ['settings' => $settings, 'custom_settings' => $csettings, 'edit_setting' => $setting, 'edit' => true]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	   		'setting_name' => 'required|unique:settings,key|max:255',
	   		//'setting_value' => 'required',
			'setting_label' => 'required',
   		]);

		$setting= new Setting;
		$setting->key = $this->parseKey($request->setting_name);
    	$setting->value = $request->setting_value;
		$setting->type = $request->setting_type;
		$setting->label = $request->setting_label;
		$setting->protected = 0;
		$setting->value_bool = $request->setting_value_bool? 1 : 0 ;
		$setting->order = 1;
		$setting->save();

		return redirect('/admin/settings')->with('success', 'Setting created.');
	}

	public function update(Request $request, $id)
	{

		$validatedData = $request->validate([
	   		'setting_name' => 'required|unique:settings,key,'.$id.'|max:255',
	   		//'setting_value' => 'required',
			'setting_label' => 'required',
   		]);

		$setting = Setting::find($id);
		$setting->key = $this->parseKey($request->setting_name);
    	$setting->value = $request->setting_value;
		$setting->label = $request->setting_label;
		$setting->type = $request->setting_type;
		$setting->value_bool = $request->setting_value_bool? 1 : 0 ;
		$setting->save();


		return redirect('/admin/settings')->with('success', 'Setting updated.');
	}

	public function delete($id)
	{
		$setting = Setting::find($id);
		if( $setting->protected ){
			return redirect('/admin/settings')->with('error', 'Cannot delete protected setting.');
		}
		$setting->delete();

		return redirect('/admin/settings')->with('success', 'Setting deleted.');
	}

	private function parseKey($text)
	{
		$text = str_replace(' ', '_', $text);
		$text = preg_replace('/[^a-zA-Z0-9_]/', '', $text);
		$text = strtolower($text);
		return $text;
	}

	public function cacheClear($type)
	{
		$cacheDriver = config('cache.default');

		switch($type){
			case 'all':
				Cache::flush();
			break;
			case 'page':
				Cache::forget('');
			break;
			case 'entry':
				Cache::tags('entry');
				Cache::tags('entry_type');
				Cache::forget('');
			break;
			case 'taxonomy':
				Cache::tags('taxonomy');
				Cache::forget('');
			break;
		}

		return redirect('/admin/settings')->with('success', 'Cache cleared.');
	}

	private function cacheClearDriver($driver, $type)
	{
		if( $driver === 'file' ){
			$storage = \Cache::getStore();
		    $filesystem = $storage->getFilesystem();
		    $dir = (\Cache::getDirectory());
		    $keys = [];
		    foreach ($filesystem->allFiles($dir) as $file1) {

		        if (is_dir($file1->getPath())) {

		            foreach ($filesystem->allFiles($file1->getPath()) as $file2) {
		                $keys = array_merge($keys, [$file2->getRealpath() => unserialize(substr(\File::get($file2->getRealpath()), 10))]);
		            }
		        }
		    }
		}
	}

}
