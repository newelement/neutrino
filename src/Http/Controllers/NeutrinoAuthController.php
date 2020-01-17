<?php
namespace Newelement\Neutrino\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Illuminate\Support\Facades\Hash;
use Newelement\Neutrino\Models\User;
use Newelement\Neutrino\Models\PasswordReset;
use Newelement\Neutrino\Mail\EmailPasswordReset;
use Illuminate\Support\Facades\Mail;

class NeutrinoAuthController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        //if ($this->guard()->user()) {
            //return redirect()->route('neutrino.dashboard');
        //}
		$data = $this->getFakeData('Login');
        return Neutrino::view('neutrino::auth.login', ['data' => $data]);
    }

    public function postLogin(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /*
     * Preempts $redirectTo member variable (from RedirectsUsers trait)
     */
    public function redirectTo()
    {
        return route('neutrino.dashboard');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return app('NeutrinoAuth');
    }


	public function getEmailResetPassword()
	{
		if ($this->guard()->user()) {
			return redirect()->route('neutrino.dashboard');
		}
		return view('neutrino::auth.email-reset-password', ['data' => $this->getFakeData('Email Password Reset') ]);
	}

	public function getResetPassword(Request $request, $email, $token)
	{
		$valid = false;
		// validate token
		$pr = PasswordReset::where([
			'email' => $email,
			'token' => $token
		])->first();

		if( $pr ){
			if($pr->created_at->addHour(3) > \Carbon\Carbon::now() ){
				$valid = true;
			}
		}

		if( !$valid ){
			return redirect('email-reset-password')->with('error', __('neutrino::messages.reset_email_invalid'));
		}

		$data = $this->getFakeData('Reset Password');
		$data->email = $email;
		$data->token = $token;

		return view('neutrino::auth.reset-password', [ 'data' => $data ]);
	}

	public function emailResetPassword(Request $request)
	{

		$validatedData = $request->validate([
        	'email' => 'required|email|max:255',
    	]);

		if( $user = User::where('email', $request->email )->first() ){

			$token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

			PasswordReset::insert([
                'email' => $user->email,
                'token' => $token,
				'created_at' => \Carbon\Carbon::now()
            ]);

			Mail::to($user->email)->send(new EmailPasswordReset($user->email, $token));

			return redirect()->back()->with('success', __('neutrino::messages.password_reset_sent'));

		} else {
			return redirect('email-reset-password')->with('error', __('neutrino::messages.could_not_find_email'));
		}


	}

	public function resetPassword(Request $request)
	{
		$validatedData = $request->validate([
        	'email' => 'required|email|max:255',
			'password' => 'required|confirmed|min:6'
    	]);

		$email = $request->email;
		$password = $request->password;

		$user = User::where('email', $email)->update([
			'password' => Hash::make($password)
		]);

		if( !$user ){
			return redirect()->back()->with('success', __('neutrino::messages.issue_updating_password'));
		}

		return redirect('/admin/login')->with('success', __('neutrino::messages.login_with_new_password'));
	}
	
	public function getRegister()
	{
    	$data = $this->getFakeData('Register');
    	return view('neutrino::auth.register', ['data' => $data]);
	}
	
	public function register(Request $request)
	{
    	$validatedData = $request->validate([
        	'name' => 'required|max:255',
        	'email' => 'required|email|max:255',
			'password' => 'required|confirmed|min:6'
    	]);

		$email = $request->email;
		$name = $request->name;
		$password = $request->password;

		User::create([
    		'name' => $name,
    		'email' => $email,
			'password' => Hash::make($password)
		]);
		
		return redirect('/')->with('success', __('neutrino::messages.registered'));
	}

	private function getFakeData($title = '', $descr = '', $keywords = '')
	{
		$data = new \stdClass;
		$data->title = $title;
		$data->meta_description = $descr;
		$data->keywords = $keywords;
		$data->data_type = 'page';

		return $data;
	}
}
