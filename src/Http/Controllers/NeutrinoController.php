<?php
namespace Newelement\Neutrino\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\EntryType;
use DB;

class NeutrinoController extends Controller
{

	public function index()
    {
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->get();
		$google_analytics_client_id = env('GOOGLE_ANALYTICS_CLIENT_ID');
        return Neutrino::view('neutrino::admin.dashboard', ['entry_types' => $entryTypes, 'google_analytics_client_id' => $google_analytics_client_id]);
    }

    public function logout()
    {
        app('NeutrinoAuth')->logout();
        return redirect()->route('login');
    }

}
