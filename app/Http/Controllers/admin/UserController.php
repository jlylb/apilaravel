<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Bouncer;
use App\User;

class UserController extends Controller
{
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
        $users = $query->paginate($perPage);
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
        ]);
       $ret = User::create($data);
       
       if($ret){
           return ['status' => 1, 'msg'=>'保存成功'];
       }else{
           return ['status' => 0, 'msg'=>'保存失败'];
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
        ]);
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
        $allRoles = Bouncer::role()->select(['name as value','title as label'])->get();
        $user = \App\User::findOrFail($id);
        $myRoles = $user->roles()->get()->pluck('name');
        return ['status' => 1, 'data'=>compact('allRoles','myRoles')];
    }

    public function updateRoles(Request $request)
    {
        $uid = $request->input('id');
        $user = \App\User::findOrFail($uid);
        Bouncer::sync($user)->roles($request->input('roles',[]));
        return ['status' => 1, 'msg'=>'success'];
    }
}
