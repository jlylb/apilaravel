<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
         
         $credentials = [
             'name' => $post['username'],
             'password' => $post['password'],
         ];

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user()->toArray();
        $user['roles'] = auth()->user()->roles()->get()->pluck('name');
        $ability = auth()->user()->getAbilities()->pluck('name')->toArray();
        $lists = \App\Menu::whereIn('route_name', $ability)
                ->orWhere('route_name', '=', '*')
                ->select(['route_name as name','route_path as path','component','redirect','meta', 'pid', 'id','hidden', 'buttons','always_show'])
                ->get()
                ->toArray();
        //$routes = [];
        foreach($lists as &$item) {
            $item['meta']= json_decode($item['meta']);
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
        
        return response()->json(compact('routes','user', 'ability'));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
