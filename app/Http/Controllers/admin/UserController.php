<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\traits\UserPrivilege;
use Bouncer;
use App\User;


class UserController extends Controller
{
    use UserPrivilege;
    
    protected $message = [
        'name.required' => '用户名称必须',
        'name.unique' => '用户名称已经存在',
        'name.max' => '用户名称不能超过255个字符',
        'email.required' => '用户邮箱必须',
        'email.unique' => '用户邮箱已经存在',
        'email.max' => '用户邮箱不能超过255个字符',
        'password.required' => '用户密码必须',
        'password.confirmed' => '两次输入密码不一致',
        'password.min' => '用户密码至少6个字符',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('name', '');
        $query = User::query();
        if(!empty($name)) {
            $query->where('name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
        $user = $this->user();
        if(!$this->isSuper($user)){
            $leader = Bouncer::role()->where('name','=','company_admin')->first();
            $ids = $leader->users()->where('company_id','=',$user->company_id)->get()->pluck('id')->toArray();
            $query->where('company_id', '=', $user->company_id)->whereNotIn('id', $ids);
        }
        $users = $query->with(['company' => function($q){
            $q -> select(['name','id']);
        }]) -> paginate($perPage);
        
        return ['status' => 1, 'data'=>$users];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data = $request->input();
       $this->validate($request, [
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ], $this->message);
       $ret = User::create($data);
       
       if($ret){
           return ['status' => 1, 'msg'=>'添加成功'];
       }else{
           return ['status' => 0, 'msg'=>'添加失败'];
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $user = User::findOrFail($id);
       $data = $request->input();
       $this->validate($request, [
            'name' => 'required|unique:users,name,'.$id.'|max:255',
            'email' => 'required|email|unique:users,email,'.$id
        ], $this->message);
       $ret = $user->update($data);
       
       if($ret){
           return ['status' => 1, 'msg'=>'保存成功'];
       }else{
           return ['status' => 0, 'msg'=>'保存失败'];
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $user = User::findOrFail($id);
       if($user->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }

    public function getRoles($id)
    {
        $user = \App\User::findOrFail($id);
        $currentUser = $this->user();
        if($this->isSuper($currentUser)){
            $allRoles = Bouncer::role()->select(['name as value','title as label'])->get();
        }else{
            $allRoles = Bouncer::role()->where('scope', '=', $user->company_id)->select(['name as value','title as label'])->get();
        }
       
        $myRoles = $user->roles()->get()->pluck('name');
        return ['status' => 1, 'data'=>compact('allRoles','myRoles')];
    }

    public function updateRoles(Request $request)
    {
        $uid = $request->input('id');
        $user = \App\User::findOrFail($uid);
        Bouncer::sync($user)->roles($request->input('roles',[]));
        return ['status' => 1, 'msg'=>'更新用户角色成功'];
    }
}
