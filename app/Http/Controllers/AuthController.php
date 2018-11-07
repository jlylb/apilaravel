<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\traits\UserPrivilege;
use Hash;
use JWTAuth;
use Bouncer;
use App\Userinfo;
use Illuminate\Support\Facades\Cache;
use Notification;

class AuthController extends Controller
{
    use UserPrivilege;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth:api', ['except' => ['login', 'sendCode', 'forgetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // $credentials = request(['name', 'password']);
        $post = $request->input();
         // dd($post);
        $credentials = $this->getCredentials($post);
        $token = $this->auth()->attempt($credentials);
        if (!$token ) {
            return response()->json(['status'=>0,'msg'=>'账号或密码错误','code'=>'4001']);
        }
        
        return $this->respondWithToken($token);
    }
    
    protected function getCredentials($post) {
        $credentials = [
             'username' => $post['username'],
             'password' => $post['password'],
         ];
        return $credentials;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo()
    {
        $userModel = $this->user();
        $userModel->load('company');
        $user = $userModel->toArray();
        $user['roles'] = $userModel->roles()->get()->pluck('name');
        $query = \App\Menu::query();
        $ability = [];
        if(!$userModel->isA('superadmin')){
            $ability = $userModel->getAbilities()->pluck('name')->toArray();
            $query->whereIn('route_name', $ability)->orWhere('route_name', '=', '*');
        }
        $lists = $query
                ->select(['route_name as name','route_path as path','component','redirect','meta', 'pid', 'id','hidden', 'buttons','always_show', 'name as menu_name'])
                ->get()
                ->toArray();
        foreach($lists as &$item) {
            if($item['name']=='*') {
                unset($item['name']);
            }
        }
        unset($item);
        
        
        $refs = [];

        foreach ($lists as $k => $v) {
            $refs[$v['id']] = &$lists[$k];
        }

        $root = 0;
        $routes = [];
        foreach ($lists as $k => $v) {
            $parentId = $v['pid'];
            if ($root == $parentId) {
                $routes[] = &$lists[$k];
            } else {
                if (isset($refs[$parentId])) {
                    $parent = &$refs[$v['pid']];
                    $parent['children'][] = &$lists[$k];
                }
            }
        }
        // $notification = $userModel->unreadNotifications()->count();
        // $user['notification'] = $notification;
        $warnNum = \App\Models\Phpwarn::count();
        $user['notification'] = $warnNum;
        $status = 1;
        return response()->json(compact('routes','user', 'ability', 'status'));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->auth()->logout();

        return response()->json(['message' => '退出成功']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status'=>1,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->auth()->factory()->getTTL() * 60
        ]);
    }


    public function saveUserInfo(Request $request) {
        $user = $this->user();
        $avatar = $request->input('logo', '');
        $user->avatar = $avatar;
        if($user->save()) {
            return ['status'=>1, 'msg'=>'个人信息修改成功'];
        }else{
            return ['status'=>0, 'msg'=>'个人信息修改失败'];
        }

    }

    public function modifyPassword(Request $request) {
        $user = $this->user();
        $this->validate($request,[
            'password' => 'required|min:6',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $password = $request->input('password');
        $newPassword = $request->input('new_password');

        if(!$this->checkPwd($password, $user->userpwd)){
            return ['status'=>0, 'msg'=>'原密码不正确'];
        }

        $user->userpwd = $newPassword ;
        if($user->save()) {
            JWTAuth::parseToken()->invalidate();
            return ['status'=>1, 'msg'=>'密码修改成功'];
        }else{
            return ['status'=>0, 'msg'=>'密码修改失败'];
        }

    }
    
    protected function checkPwd($password,$srcPassword) {
        return md5(trim($password))===$srcPassword;
    }
    
    //发送短信
    protected function sendSms($phone, $code) {
        $invoice = [
            'phone'=>$phone,
            'content'=>'您好,当前验证码是'.$code,
        ];
        $when = \Carbon\Carbon::now()->addMinutes(1);
        $ret = Notification::send($phone, (new \App\Notifications\Phone($invoice))->delay($when));
        return true;
    }
    //生成验证码
    protected function getVerifyCode($len=6) {
        $start = str_pad(1, $len, 0);
        $end = str_pad(9, $len, 9);
        return mt_rand($start, $end);
    }
    
    //发送验证码
    public function sendCode(Request $request) {
        $this->validate($request,[
            'phone'=>'required|digits:11|regex:/^1[3578]\d{9}$/',
        ]);
        $phone = $request->input('phone');
        $code = $this->getVerifyCode();
        $ret = $this->sendSms($phone, $code);
        if($ret) {
            Cache::put('code:'.$phone, $code, config('auth.code_expire'));
            return ['status'=>1, 'msg'=>'发送短信成功'];
        }else{
            return ['status'=>0, 'msg'=>'发送短信失败'];
        }
    }
    
    //检查验证码是否过期
    protected function checkCodeExpire($phone) {
        return Cache::get('code:'.$phone);
    }
    //检查验证码是否相等
    protected function checkCodeEqual($phone, $code) {
        return Cache::get('code:'.$phone)==$code;
    }
    //找回密码
    public function forgetPassword(Request $request) {
        $user = $this->user();
        if($user) {
           JWTAuth::parseToken()->invalidate(); 
        }
        $this->validate($request,[
            'phone'=>'required|digits:11|regex:/^1[3578]\d{9}$/',
            'code' => 'required|min:6',
            'password' => 'required|min:6',
        ]);
        $code = $request->input('code');
        $phone = $request->input('phone');
        if(!$this->checkCodeExpire($phone)) {
            return ['status'=>0, 'msg'=>'验证码已过期'];
        }
        if(!$this->checkCodeEqual($phone, $code)) {
            return ['status'=>0, 'msg'=>'验证码输入错误'];
        }
        $userinfo = Userinfo::where('userphone', '=', trim($phone))->first();
        if(!$userinfo) {
           return ['status'=>0, 'msg'=>'手机号不存在'];
        }
        $muser = $userinfo->user;
        $password = $request->input('password');
        $muser->userpwd = $password;
        if($muser->save()) {
            return ['status'=>1, 'msg'=>'找回密码成功'];
        }else{
            return ['status'=>0, 'msg'=>'找回密码失败'];
        }

    }
}
