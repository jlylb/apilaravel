<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DeviceType;

class DevicetypeController extends Controller
{
    
    protected $message = [
        
        'dt_typeid.required' => '设备类型编号必须',
        'dt_typeid.integer' => '设备类型编号必须是数字',
        
        'dt_typename.required' => '设备类型名称必须',
        'dt_typename.max' => '设备类型名称不能超过64个字符',
        
        'dt_rtdata_table.required' => '实时数据表名必须',
        'dt_rtdata_table.max' => '实时数据表名不能超过64个字符',
        
        'dt_hisdata_table.required' => '历史数据表名必须',
        'dt_hisdata_table.max' => '历史数据表名不能超过64个字符',

    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('dt_typename', '');
        $query = DeviceType::query();
        if(!empty($name)) {
            $query->where('dt_typename', 'like', $name.'%');
        }
        $users = $query-> paginate($perPage);
        
        return ['status' => 1, 'data'=>$users];
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
       $this->validateData($request);
       $ret = DeviceType::create($data);
       
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
       $deviceType = DeviceType::findOrFail($id);
       $data = $request->input();
       $this->validateData($request);
       $ret = $deviceType->update($data);
       
       if($ret){
           return ['status' => 1, 'msg'=>'保存成功'];
       }else{
           return ['status' => 0, 'msg'=>'保存失败'];
       }
    }
    
    protected function validateData($request) {
        $this->validate($request, [
            'dt_typeid' => 'required|integer',
            'dt_typename' => 'required|max:64',
            'dt_rtdata_table' => 'required|max:64',
            'dt_hisdata_table' => 'required|max:64'
        ], $this->message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $deviceType = DeviceType::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
}
