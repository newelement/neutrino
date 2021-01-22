<?php
namespace Newelement\Neutrino\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class NeutrinoAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        auth()->setDefaultDriver(app('NeutrinoGuard'));

        if (!Auth::guest()) {
            $user = Auth::user();
            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasRole('admin') || $user->hasRole('editor') ? $next($request) : redirect('/');
        }

        $urlLogin = route('login');
        return redirect()->guest($urlLogin);
    }
}
