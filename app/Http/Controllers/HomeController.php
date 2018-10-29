<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendReminderEmail;
use App\Notifications\InvoicePaid;
use Route;
use Bouncer;
use Mail;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware([
                // 'auth'
                //, 'permission'
                //  , 'scope'
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $ret = Bouncer::role()->where('name', '=', 'company_admin')->first();
        return view('home');
    }

    public function welcome() {
        return view('welcome');
    }

    public function genPermission() {
        foreach (Route::getRoutes() as $route) {
            $routeName = $route->getName();
            if (strpos($routeName, 'notification') !== false) {
                continue;
            }
            if (strpos($routeName, 'api') === false) {
                continue;
            }
            $curPrefix = trim($route->getPrefix(), '/') ?: '';

            if (!$routeName) {
                continue;
            }

            $routePath = $route->getPath();
            $title = str_replace('.', ' ', $routeName);

            $desc = config('ability');

            Bouncer::ability()->firstOrCreate([
                'name' => $routeName,
                'title' => isset($desc[$routeName])?$desc[$routeName]:$title,
                'route_path' => '/' . trim(str_replace($curPrefix, '', $routePath), '/')
            ]);
        }
    }

    public function send() {
        $name = 'vilin';
        $flag = Mail::send('emails.test', ['name' => $name], function($message) {
                    $to = '395458341@qq.com';
                    $message->to($to)->subject('邮件主题');
                });
        if ($flag) {
            echo '发送邮件成功，请查收！';
        } else {
            echo '发送邮件失败，请重试！';
        }
    }

    public function sendReminderEmail(Request $request, $id) {
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
            'content' => '机房主机出现重要故障，请速查看',
            'status' => 'enemegy'
        ]));
    }

}
