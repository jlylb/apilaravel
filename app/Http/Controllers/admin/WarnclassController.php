<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\WarnClass;

class WarnclassController extends Controller
{
    protected $message = [
             
        'Wc_classname.required' => '告警分类必须',
        'Wc_classname.max' => '告警分类不能超过64个字符',
        'Wc_memo.max' => '告警描述不能超过255个字符',

    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('Wc_classname', '');
        $query = WarnClass::query();
        if(!empty($name)) {
            $query->where('Wc_classname', 'like', trim($name).'%');
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
       $ret = WarnClass::create($data);
       
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
       $deviceType = WarnClass::findOrFail($id);
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
            'Wc_classname' => 'required|max:64',
            'Wc_memo' => 'max:255',
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
       $deviceType = WarnClass::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
    
}
