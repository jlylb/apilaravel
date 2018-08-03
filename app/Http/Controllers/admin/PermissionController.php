<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Bouncer;

class PermissionController extends Controller
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
        $query = Bouncer::ability()->query();
        if(!empty($name)) {
            $query->where('name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
        $permissions = $query->paginate($perPage);
        return ['status' => 1, 'data'=>$permissions];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required|unique:permissions|max:155',
            'title' => 'required|max:255',
        ]);
        $ability = Bouncer::ability()->create($data);
        if($ability){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
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
        $data = $request->input();
        $this->validate($request, [
            'name' => 'required|unique:permissions,name,'.$id.'|max:155',
            'title' => 'required|max:255',
        ]);
        $ability = Bouncer::ability()->findOrFail($id);
        if($ability->update($data)){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
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
        $ability = Bouncer::ability()->findOrFail($id);
        if($ability->delete()){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
        }
    }

    public function search(Request $request, $name)
    {
        $query = Bouncer::ability();
        $permissions = $query->where('name', 'like', '%'.$name.'%')
        ->select('name as value', 'title as label', 'route_path')
        ->get();
        return ['status' => 1, 'data'=>$permissions];
    }
}
