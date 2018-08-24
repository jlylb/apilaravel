<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
//    public function handle($request, Closure $next, $guard = null)
//    {
//        if (Auth::guard($guard)->guest()) {
//            if ($request->ajax() || $request->wantsJson()) {
//                return response('Unauthorized.', 401);
//            } else {
//                return redirect()->guest('login');
//            }
//        }
//
//        return $next($request);
//    }
    
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    
        public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            return $this->auth->authenticate();
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException('Unauthenticated.', $guards);
        // return response()->json(['msg'=>'没有权限访问'],401);
    }
}