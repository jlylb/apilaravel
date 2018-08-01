<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Route;
use Bouncer;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->can('*');
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function genPermission()
    {
        foreach(Route::getRoutes() as $route){
            $curAction = $route->getActionName();
            $curPrefix = trim($route->getPrefix(),'/');
            $prefix = [];
            if($curPrefix) {
                $prefix = explode('/', $curPrefix);
            }
            $curAction = explode('\\', $curAction);

            $controller = strtolower(str_replace('Controller', '', end($curAction)));
            $actions = array_merge($prefix, explode('@', $controller));
            $name = implode('_', $actions);
            $title = implode(' ', $actions);

            Bouncer::ability()->firstOrCreate([
                'name' => $name,
                'title' => $title,
            ]);
        }
    }
}
