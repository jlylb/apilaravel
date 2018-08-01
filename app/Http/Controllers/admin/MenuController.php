<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $menu = Menu::paginate($perPage);
        return ['status' => 1, 'data'=>$menu];
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
        $data = array_except(json_decode($request->getContent(), true), 'action');
        $menu = Menu::firstOrCreate($data);
        if($menu){
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
        $data = array_except(json_decode($request->getContent(), true), 'action');
        $menu = Menu::findOrFail($id);
        if($menu->update($data)){
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
        $menu = Menu::findOrFail($id);
        if($menu->delete()){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
        }
    }
}
