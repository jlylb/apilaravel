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

        $name = $this->getPermissionName(Route::getCurrentRoute());
        if(!Bouncer::can($name)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        
        return $next($request);
    }

    protected function getPermissionName($route) {
        $curAction = $route->getActionName();
        $curPrefix = trim($route->getPrefix(),'/');
        $prefix = [];
        if($curPrefix) {
            $prefix = array_filter(explode('/', $curPrefix));
        }
        $curAction = explode('\\', $curAction);

        $controller = strtolower(str_replace('Controller', '', end($curAction)));
        $actions = array_merge($prefix, explode('@', $controller));
        return implode('_', $actions);
    }
}
