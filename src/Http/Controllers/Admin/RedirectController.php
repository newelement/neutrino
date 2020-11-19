<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Redirect;
use Newelement\Neutrino\Models\ActivityLog;

class RedirectController extends Controller
{
    public function __construct(){}

    public function index()
    {
        $redirects = Redirect::paginate(30);

        return view('neutrino::admin.redirects.index', ['redirects' => $redirects]);
    }

    public function get(Request $request, $id)
    {
        $redirect = Redirect::findOrFail($request->id);

        return view('neutrino::admin.redirects.edit', ['redirect' => $redirect]);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'old_url' => 'required',
            'new_url' => 'required',
        ]);

        $redirect = new Redirect;
        $redirect->old_url = $request->old_url;
        $redirect->new_url = $request->new_url;
        $redirect->status = (int) $request->status;
        $redirect->save();

        return redirect('/admin/redirects/')->with('success', 'Redirect created.');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'old_url' => 'required',
            'new_url' => 'required',
        ]);

        $redirect = Redirect::findOrFail($request->id);
        $redirect->old_url = $request->old_url;
        $redirect->new_url = $request->new_url;
        $redirect->status = (int) $request->status;
        $redirect->save();

        return redirect('/admin/redirects/'.$redirect->id)->with('success', 'Redirect updated.');
    }

    public function delete(Request $request)
    {
        $redirect = Redirect::findOrFail($request->id);
        $redirect->delete();

        return redirect('/admin/redirects/')->with('success', 'Redirect deleted.');
    }

}
