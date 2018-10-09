<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Warnnotify;

class WarnnotifyController extends Controller
{
    protected $message = [
        'pdi_index.required' => '设备编号不能为空',
        'pdi_index.number' => '设备编号非法',
        'pdi_index.exists' => '设备编号不存在',
        'Wn_notifytype.required' => '告警方式不能为空',
        'Wn_notifytype.number' => '告警方式非法',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('pdi_index', '');
        $query = Warnnotify::query();
        if(!empty($name)) {
            $query->where('pdi_index', '=', trim($name));
        }
        $query->with(['user'=>function($query){
            $query->select(['wu_index','Wu_name']);
        }]);
        $warns = $query-> paginate($perPage)->toArray();
        if($warns['data']) {
            $warns['data'] = array_map(function($v){
                $v['Wn_notifytype'] = $this->getTypeValue($v['Wn_notifytype']);
                return $v;
            }, $warns['data']);
        }
        
        return ['status' => 1, 'data'=>$warns];
    }
    
    protected function getTypeValue($value) {
        $arr = config('device.notify_type');

        return array_reduce($arr, function($carry, $v)use($value){
            $cur=$value & $v ;
            if($cur){
                $carry[]=$cur + 0;
            }
            return $carry;
        },[]);
        
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
       $ret = Warnnotify::create($data);
       
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
        $wdefine = Warnnotify::findOrFail($id);

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
       $deviceType = Warnnotify::findOrFail($id);
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
            'pdi_index' => 'required|numeric|exists:t_prideviceinfo,pdi_index',
            'Wn_notifytype' => 'required',
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
       $deviceType = Warnnotify::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
}
