<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use Bouncer;
use Illuminate\Auth\AuthenticationException;

class CheckPermissiion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard=null)
    {

        $name = $this->getPermissionName(Route::getCurrentRoute());
        if(!$guard) {
            $guard = 'web';
        }
        $user = auth($guard)->user();
        if(Bouncer::is($user)->a('superadmin')){
            return $next($request);
        }
        //var_dump(Bouncer::can($name),$name);
         if(!$user->can($name)) {
             return response()->json(['error' => 'Unauthenticated.'], 403);
         }
        
        return $next($request);
    }

    protected function getPermissionName($route) {
        $name = $route->getName();
        return $name;
    }
}
