<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Role;
use Illuminate\Support\Str;
use Newelement\Neutrino\Models\ActivityLog;

class RolesController extends Controller
{
	public function index()
	{
		$roles = Role::orderBy('name', 'asc')->get();
		$role = new \stdClass();
		$role->name = '';
		$role->display_name = '';
		$role->id = '';
		return view( 'neutrino::admin.roles.index', ['roles' => $roles, 'edit_role' => $role, 'edit' => false]);
	}

	public function get($id)
	{
		$role = Role::find($id);
		$roles = Role::orderBy('name', 'asc')->get();
		return view( 'neutrino::admin.roles.index', ['roles' => $roles, 'edit_role' => $role, 'edit' => true]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	   		'name' => 'required|unique:roles,key|max:255',
	   		'display_name' => 'required',
   		]);

		$setting= new Role;
		$role->name = $this->parseKey($request->name);
    	$role->display_name = $request->display_name;
		$role->save();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'role.create',
            'object_type' => 'role',
            'object_id' => $role->id,
            'content' => 'Role created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/roles')->with('success', 'Role created.');
	}

	public function update(Request $request, $id)
	{

		$validatedData = $request->validate([
	   		'name' => 'required|unique:roles,name,'.$id.'|max:255',
	   		'display_name' => 'required',
   		]);

		$role = Role::find($id);

		if( $role->name === 'admin' ){
			return redirect('/admin/roles')->with('error', 'Not allowed to edit admin role.');
		}

		$role->name = $this->parseKey($request->name);
    	$role->display_name = $request->display_name;
		$role->save();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'role.updated',
            'object_type' => 'role',
            'object_id' => $role->id,
            'content' => 'Role updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/roles')->with('success', 'Role updated.');
	}

	public function delete($id)
	{
		Role::find($id)->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'role.delete',
            'object_type' => 'role',
            'object_id' => $id,
            'content' => 'Role deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/roles')->with('success', 'Role deleted.');
	}

	private function parseKey($text)
	{
		$text = str_replace(' ', '_', $text);
		$text = preg_replace('/[^a-zA-Z0-9_]/', '', $text);
		$text = strtolower($text);
		return $text;
	}
}
