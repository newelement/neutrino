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
use Newelement\Neutrino\Models\ActivityLog;

class SettingsController extends Controller
{
	public function index()
	{
		$settings = Setting::where('protected', 1)->orderBy('key', 'asc')->get();
		$csettings = Setting::where('protected', 0)->orderBy('key', 'asc')->get();
        $health = $this->checkHealth();

        $sitemapSettings = \DB::table('sitemap')->get();
        $sitemap_settings = $sitemapSettings[0];

		$setting = new \stdClass();
		$setting->key = '';
		$setting->value = '';
		$setting->type = '';
		$setting->value_bool = 0;
		$setting->label = '';
		$setting->id = '';
		$setting->protected = 0;
		return view( 'neutrino::admin.settings.index', [
            'settings' => $settings,
            'custom_settings' => $csettings,
            'edit_setting' => $setting,
            'sitemap_settings' => $sitemap_settings,
            'health' => $health,
            'edit' => false]);
	}

	public function get($id)
	{
		$setting = Setting::find($id);
		$settings = Setting::where('protected', 1)->orderBy('key', 'asc')->get();
		$csettings = Setting::where('protected', 0)->orderBy('key', 'asc')->get();

        $sitemapSettings = \DB::table('sitemap')->get();
        $sitemap_settings = $sitemapSettings[0];

		return view( 'neutrino::admin.settings.index', ['settings' => $settings, 'custom_settings' => $csettings, 'sitemap_settings' => $sitemap_settings, 'edit_setting' => $setting, 'edit' => true]);
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

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'setting.create',
            'object_type' => 'setting',
            'object_id' => $setting->id,
            'content' => 'Setting created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

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

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'setting.update',
            'object_type' => 'setting',
            'object_id' => $setting->id,
            'content' => 'Setting updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/settings')->with('success', 'Setting updated.');
	}

	public function delete($id)
	{
		$setting = Setting::find($id);
		if( $setting->protected ){
			return redirect('/admin/settings')->with('error', 'Cannot delete protected setting.');
		}
		$setting->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'setting.delete',
            'object_type' => 'setting',
            'object_id' => $id,
            'content' => 'Setting deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

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
                \Storage::disk('public')->delete('assets/css/all.css');
                \Storage::disk('public')->delete('assets/js/all.js');
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

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'setting.cache.clear',
            'object_type' => 'setting',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/settings')->with('success', 'Cache cleared.');
	}

    public function clearAssetCache()
    {
        \Storage::disk('public')->delete('assets/css/all.css');
        \Storage::disk('public')->delete('assets/js/all.js');

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'setting.asset.clear',
            'object_type' => 'setting',
            //'object_id' => $id,
            //'content' => 'Setting deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return redirect('/admin/settings')->with('success', 'Asset cache cleared.');
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

    public function getActivityLog(){
        $logs = ActivityLog::orderBy('created_at', 'desc')->paginate(50);

        $set = [];

        foreach( $logs as $log ){
            $set[] = [
                'id' => $log->id,
                'package' => $log->activity_package,
                'group' => $log->activity_group,
                'object' => '',
                'activity' => $log->content,
                'user' => $log->createdUser? $log->createdUser->name : 'Guest',
                'level' => $log->log_level,
                'created_on' => $log->created_at->timezone( config('neutrino.timezone') )->format('m-j-y g:i a')
            ];
        }

        $arr = [
            'last_page' => $logs->lastPage(),
            'data' => $set
        ];

        return response()->json($arr);
    }

    private function checkHealth()
    {
        //try{
            $thisBond = file_get_contents(base_path('vendor/newelement/neutrino/').'bond.json');
            $latestBond = file_get_contents('https://raw.githubusercontent.com/newelement/neutrino/master/bond.json');

            dd(json_decode($thisBond));

        //} catch( \Exception $e ) {
            // void
        //}

    }

}
