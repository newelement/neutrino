<?php
namespace Newelement\Neutrino\Http\Middleware;
use Closure;

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
        if (!app('NeutrinoAuth')->guest()) {
            $user = app('NeutrinoAuth')->user();
            return $user->hasRole('admin') || $user->hasRole('editor') ? $next($request) : redirect('/');
        }
        $urlLogin = route('login');
        return redirect()->guest($urlLogin);
    }
}
