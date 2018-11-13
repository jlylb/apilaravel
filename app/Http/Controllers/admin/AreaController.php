<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Area;

class AreaController extends Controller
{
    
    protected $message = [
        'province.required' => '省级区域不能为空',
       // 'province.exists' => '省级区域不存在，请先添加',
        
        'AreaName.required' => '区域名称必须',
        'AreaName.max' => '区域名称不能超过60个字符',
        'AreaName.unique' => '区域名称已添加',
        'Co_ID.required' => '所属公司必须',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('AreaName', '');
        $query = Area::query();
        if(!empty($name)) {
            $query->where('AreaName', 'like', trim($name).'%');
        }
        $all = $request->input('total', false);
        if($all) {
            $query->where('Fid', '>', 0);
         }
        $aid = $request->input('aid', '');
        if($aid) {
            $query->where('Fid', '=', $aid);
         }
        $query->from('sy_area');
        $query->leftjoin('t_companycnfo as b', 'sy_area.Co_ID', '=', 'b.Co_ID');
        $query->select(['sy_area.*','b.Co_Name']);
        $query->with(['parentAreaName' => function($query) use($request){
            $query->select(['AreaId','AreaName','Fid']);
        }]);
        $users = $query -> paginate($perPage);
        
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
        $this->validate($request, [
            'AreaName'=>'required|max:60|unique:sy_area,AreaName,NULL,NULL,Co_ID,'.$data['Co_ID'],
            'province'=>'required',
            'Co_ID'=>'required'
        ], $this->message);
        if(empty($data['city'])){
            $data['Fid'] = 0;
            $ret = Area::create($data);
        }else{
            $parentInfo = Area::where('AreaName', '=', trim($data['province']))
                    ->where('Co_ID', '=', trim($data['Co_ID']))
                    ->where('Fid', '=', 0)
                    ->first();
            if(!$parentInfo){
                return ['status' => 0, 'msg'=>'省级区域不存在,请先添加 '];
            }
            $data['Fid'] = $parentInfo->AreaId;
            $ret = Area::create($data);
        }

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
        $area = Area::findOrFail($id);
        $data = $request->input();
        $this->validate($request, [
            'AreaName'=>'required|max:60|unique:sy_area,AreaName,'.$id.',AreaId,Co_ID,'.$data['Co_ID'],
            'province'=>'required',
            'Co_ID'=>'required'
        ], $this->message);
        if(empty($data['city'])){
            $data['Fid'] = 0;
            $count = Area::where('Fid', '=', $id)->count();
            if($count > 0) {
                return ['status' => 0, 'msg'=>'已存在下级区域,不可更改'];
            }
        }else{
            $parentInfo = Area::where('AreaName', '=', trim($data['province']))
                    ->where('Co_ID', '=', trim($data['Co_ID']))
                    ->where('Fid', '=', 0)
                    ->first();
            if(!$parentInfo){
                return ['status' => 0, 'msg'=>'省级区域不存在,请先添加 '];
            }
            if($area->Fid==0) {
                return ['status' => 0, 'msg'=>'省级区域不可更改,请添加下级区域'];
            }
            if($parentInfo->AreaId == $id){
                $count = Area::where('Fid', '=', $id)->count();
                if($count > 0) {
                    return ['status' => 0, 'msg'=>'已存在下级区域,不可更改'];
                }
                $data['Fid'] = 0;
            }else{
                $data['Fid'] = $parentInfo->AreaId;
            }
            
        }
        if($area->update($data)){
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
        $count = Area::where('Fid', '=', $id)->count();
        if($count > 0) {
            return ['status' => 0, 'msg'=>'已存在下级区域,不可删除'];
        }
        $area = Area::findOrFail($id);
        if($area->delete()){
            return ['status' => 1, 'msg'=>'删除成功'];
        }else{
            return ['status' => 0, 'msg'=>'删除失败'];
        }
    }
}
