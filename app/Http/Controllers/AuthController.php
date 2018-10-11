<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\traits\UserPrivilege;
use Hash;
use JWTAuth;
use Bouncer;

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
       $this->middleware('auth:api', ['except' => ['login']]);
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
        return response()->json(compact('routes','user', 'ability'));
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
}
