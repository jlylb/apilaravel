<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PriDeviceInfo;
use App\Http\traits\Utils;

class DeviceinfoController extends Controller
{
    use Utils;
    
    protected $message = [
             
        'pdi_name.required' => '设备名称必须',
        'pdi_name.max' => '设备名称不能超过64个字符',
        
        'pdi_code.required' => '设备编号必须',
        'pdi_code.max' => '设备编号不能超过64个字符',
        
        'dpt_id.required' => '设备分类编号必须',
        'dpt_id.integer' => '设备分类编号必须是数字',
        'dpt_id.exists' => '设备分类不存在',
        
        'AreaId.required' => '区域必须',
        'AreaId.integer' => '区域必须是数字',
        
        'Co_ID.required' => '公司必须',
        'Co_ID.integer' => '公司必须是数字',
        'Co_ID.exists' => '公司不存在',

    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('pdi_code', '');        
        $query = PriDeviceInfo::query();
        if(!empty($name)) {
            $query->where('pdi_code', 'like', $name.'%');
        }
        $typeId = $request->input('dpt_id', '');
        if(!empty($typeId)) {
            $query->where('dpt_id', '=', trim($typeId));
        }
        $query->with([
            'company'=>function($query){
                $query->select(['Co_ID','Co_Name']);
            },
            'area'=>function($query){
                $query->select(['AreaId','AreaName']);
            },
        ]);
        $uid = $request->input('uid', '');
        $query->select(['t_prideviceinfo.*']);
        if($uid) {
            $query->leftJoin('t_warnnotify', function($join)use($uid){
                $join->on('t_prideviceinfo.pdi_index', '=', 't_warnnotify.pdi_index')
                    ->where('wu_index', '=', $uid);
            });
            $query->addSelect(['wu_index', 'Wn_notifytype']);
        }
        $area = $request->input('area', []);
        if(!empty($area)) {
            $query->whereIn('AreaId', $area);
        }
        $users = $query-> paginate($perPage);
        
        $query2 = PriDeviceInfo::query();
        $query2->with([
            'area'=>function($query){
                $query->select(['AreaId','AreaName']);
            },
        ]);
 
        $arealist = $query2
                ->selectRaw('distinct(AreaId)')
                ->get()
                ->pluck('area')
                ->all();
        $areaNames = [];
        foreach ($arealist as  $v) {
            $areaNames[] = ['text'=>$v['AreaName'], 'value'=>$v['AreaId']];
        }
        return ['status' => 1, 'data'=>$users, 'areaName' => $areaNames ];
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
       $ret = PriDeviceInfo::create($data);
       
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
       $deviceType = PriDeviceInfo::findOrFail($id);
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
            'pdi_name' => 'required|max:64',
            'pdi_code' => 'required|max:64',
            'dpt_id' => 'required|integer|exists:t_devicetype,dt_typeid',
            'AreaId' => 'required|integer',
            'Co_ID' => 'required|integer|exists:t_companycnfo,Co_ID',
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
       $deviceType = PriDeviceInfo::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
    
    public function getDeviceType() {
        $types = \App\DeviceType::where('dt_isenable',1)
                ->select(['dt_typeid as value','dt_typememo as label'])
                ->get()
                ->toArray();
        return ['status' => 1, 'data' => $types];
    }
    
    /**
     * 获取公司所有区域
     * @return Array
     */
    public function getCompanyArea($id) {
        $company = \App\Company::findOrFail($id);
        $areas = $company
                ->areas()
                ->select(['AreaId as value','AreaName as label','Fid'])
                ->get()
                ->toArray();
        $tree = $this->listToTree($areas, 'value', 'Fid');
        return ['status' => 1, 'data' => $tree ];
    }
}
