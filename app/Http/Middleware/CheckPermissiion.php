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
    public function handle($request, Closure $next)
    {

        // $name = $this->getPermissionName(Route::getCurrentRoute());
        // if(!Bouncer::can($name)) {
        //     return response()->json(['error' => 'Unauthenticated.'], 401);
        // }
        
        return $next($request);
    }

    protected function getPermissionName($route) {
        $curAction = $route->name();
        return $name;
    }
}
