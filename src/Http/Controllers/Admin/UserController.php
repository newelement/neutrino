<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\User;
use Newelement\Neutrino\Models\Role;
use Illuminate\Support\Str;
use Newelement\Neutrino\Models\ActivityLog;

class UserController extends Controller
{
	public function index()
	{
		$users = User::orderBy('name', 'asc')->paginate(20);
		return view( 'neutrino::admin.users.index', ['users' => $users]);
	}

	public function get($id)
	{
		$user = User::find($id);
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.users.edit', ['user' => $user, 'roles' => $roles]);
	}

	public function getCreate()
	{
		$roles = Role::orderBy('display_name', 'asc')->get();
		return view( 'neutrino::admin.users.create', ['roles' => $roles]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	   		'name' => 'required|max:255',
	   		'email' => 'required|unique:users',
			'password' => 'required|min:8|confirmed'
   		]);

		$avatar = $request->avatar;
		$avatar = parse_url($avatar, PHP_URL_PATH);

		$user = new User;
		$user->name = $request->name;
    	$user->email = $request->email;
        $user->password = Hash::make($request->password);
		$user->avatar = $avatar;
		$user->role_id = $request->role;
		$user->api_token = Str::random(40);
		$user->save();

		// Do we email user the credentials?
		if( $request->email_user ){

		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'user.create',
            'object_type' => 'user',
            'object_id' => $user->id,
            'content' => 'User created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/users')->with('success', 'User created.');
	}

	public function update(Request $request, $id)
	{
		$v = [
	   		'name' => 'required|max:255',
	   		'email' => 'required|unique:users,email,'.$id
   		];

		if( $request->password ){
			$v['password'] = 'required|min:8|confirmed';
		}

		$validatedData = $request->validate($v);

		$avatar = $request->avatar;
		$avatar = parse_url($avatar, PHP_URL_PATH);

		$user = User::find($id);
		$user->name = $request->name;
    	$user->email = $request->email;
		if($request->password){
        	$user->password = Hash::make($request->password);
		}
		$user->avatar = $avatar;
		$user->role_id = $request->role;
		$user->save();

		// Do we email user the credentials?
		if( $request->email_user ){

		}

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'user.update',
            'object_type' => 'user',
            'object_id' => $user->id,
            'content' => 'User updated',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/users')->with('success', 'User updated.');
	}

	public function delete($id)
	{
		User::find($id)->delete();

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'user.delete',
            'object_type' => 'user',
            'object_id' => $id,
            'content' => 'User deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

		return redirect('/admin/users')->with('success', 'User deleted.');
	}
}
