<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WarnDefine;

class WarndefineController extends Controller
{
    protected $message = [
             
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('wd_id', '');
        $query = WarnDefine::query();
        if(!empty($name)) {
            $query->where('wd_id', '=', trim($name));
        }

        $warns = $query-> paginate($perPage);
        
        return ['status' => 1, 'data'=>$warns];
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
       $ret = WarnDefine::create($data);
       
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
        $wdefine = WarnDefine::findOrFail($id);

        return ['status' => 1, 'msg'=>'', 'data'=>$wdefine];
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
       $deviceType = WarnDefine::findOrFail($id);
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
//        $indexRule = 'required|integer|unique:t_warnclass,wc_index';
//        $index = $request->input('isAdd');
//        if(!$index) {
//            $indexRule.=','.$request->input('wc_index').',wc_index';
//        }
        $this->validate($request, [
//            'Wc_classname' => 'required|max:64',
//            'Wc_memo' => 'max:255',
          //  'wc_index' => $indexRule,
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
       $deviceType = WarnDefine::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
}
