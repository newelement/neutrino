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
        $data = [
            'entry_types' => $entryTypes,
            'google_analytics_client_id' => $google_analytics_client_id
        ];
        if( shoppeExists() ){
            $data['orderCount'] = getNewOrderCount();
            $shoppeController = new \Newelement\Shoppe\Http\Controllers\Admin\ShoppeAnalyticsController;
            $shoppeData = $shoppeController->getDasboardWidgetData();
            $data['sales_today'] = $shoppeData['sales_today'];
            $data['sales_yesterday'] = $shoppeData['sales_yesterday'];
            $data['active_carts'] = $shoppeData['active_carts'];
        }
        return Neutrino::view('neutrino::admin.dashboard', $data);
    }

    public function logout()
    {
        app('NeutrinoAuth')->logout();
        return redirect()->route('login');
    }

}
