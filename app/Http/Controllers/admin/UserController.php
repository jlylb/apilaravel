<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\traits\UserPrivilege;
use Bouncer;
use App\SyUser as User;


class UserController extends Controller
{
    use UserPrivilege;
    
    protected $message = [
        'username.required' => '用户名称必须',
        'username.unique' => '用户名称已经存在',
        'username.max' => '用户名称不能超过255个字符',
//        'email.required' => '用户邮箱必须',
//        'email.unique' => '用户邮箱已经存在',
//        'email.max' => '用户邮箱不能超过255个字符',
        'userpwd.required' => '用户密码必须',
        'userpwd.confirmed' => '两次输入密码不一致',
        'userpwd.min' => '用户密码至少6个字符',
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
            $query->where('username', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
        $user = $this->user();
        if(!$this->isSuper($user)){
            $leader = Bouncer::role()->where('name','=','company_admin')->first();
            $ids = $leader->users()->where('Co_ID','=',$user->Co_ID)->get()->pluck('userid')->toArray();
            $query->where('Co_ID', '=', $user->Co_ID)->whereNotIn('userid', $ids);
        }
        $users = $query->with(['company' => function($q){
            $q -> select(['Co_Name','Co_ID']);
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
            'username' => 'required|unique:sy_user|max:255',
           // 'email' => 'required|email|unique:users',
            'userpwd' => 'required|confirmed|min:6',
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
            'username' => 'required|unique:sy_user,username,'.$id.',userid|max:255',
            // 'email' => 'required|email|unique:users,email,'.$id
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
        $user = User::findOrFail($id);
        $currentUser = $this->user();
        if($this->isSuper($currentUser)){
            $allRoles = Bouncer::role()->select(['name as value','title as label'])->get();
        }else{
            $allRoles = Bouncer::role()->where('scope', '=', $user->Co_ID)->select(['name as value','title as label'])->get();
        }
       
        $myRoles = $user->roles()->get()->pluck('name');
        return ['status' => 1, 'data'=>compact('allRoles','myRoles')];
    }

    public function updateRoles(Request $request)
    {
        $uid = $request->input('id');
        $user = User::findOrFail($uid);
        Bouncer::sync($user)->roles($request->input('roles',[]));
        return ['status' => 1, 'msg'=>'更新用户角色成功'];
    }
}
