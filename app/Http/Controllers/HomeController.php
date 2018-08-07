<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Jobs\SendReminderEmail;
use App\Notifications\InvoicePaid;
use Route;
use Bouncer;
use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
//            'auth',
//            'permission'
//            , 'scope'
            ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $res = $request->user()->getAbilities()->pluck('name')->toArray();
        //var_dump($res);
              $menu = \App\Menu::whereIn('route_name', $res)
                ->orWhere('route_name', '=', '*')->get()->toArray();
               // Bouncer::allow($request->user())->toOwn(\App\Menu::class)->to(['view', 'update']);
        return view('home');
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function genPermission()
    {
        foreach(Route::getRoutes() as $route){
            $routeName = $route->getName();
             if(strpos($routeName,'notification')===false){
                 continue;
             }
            $curPrefix = trim($route->getPrefix(),'/')?:'';

            if(!$routeName) {
                continue;
            }
            $routePath = $route->getPath();
            $title = str_replace('.', ' ', $routeName);

            // var_dump($routeName, $routePath, $title);

            Bouncer::ability()->firstOrCreate([
                'name' => $routeName,
                'title' => $title,
                'route_path' => '/'.trim(str_replace($curPrefix, '', $routePath), '/')
            ]);
        }
    }
    public function send()  
    {  
        $name = 'vilin';  
        $flag = Mail::send('emails.test',['name'=>$name],function($message){  
            $to = '395458341@qq.com';  
            $message ->to($to)->subject('邮件主题');  
        });  
        if($flag){  
            echo '发送邮件成功，请查收！';  
        }else{  
            echo '发送邮件失败，请重试！';  
        }  
    } 
    
    public function sendReminderEmail(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);

        $this->dispatch(new SendReminderEmail($user));
    }
    
    public function sendNotifaction($id) {
        $user = \App\User::findOrFail($id);
        $user->notify(new InvoicePaid());
    }
    
    public function sendNotifaction2($id) {
        $user = \App\User::findOrFail($id);
        $user->notify(new \App\Notifications\AlarmNotice([
            'content'=>'机房主机出现重要故障，请速查看',
            'status'=>'enemegy'
        ]));
    }
}
