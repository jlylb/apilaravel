<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Warnuser;

use DB;

class WarnuserController extends Controller
{
        protected $message = [
            'Wu_name.required' => '姓名不能为空',
            'Wu_SmsNumber.required' => '短信息号码不能为空',
        ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $uid = $request->input('wu_index', '');
        $name = urldecode($request->input('Wu_name', ''));
        $query = Warnuser::query();
        
        if(!empty($uid)) {
            $query->where('wu_index', '=', trim($uid));
        }

        if(!empty($name)) {
            $query->where('Wu_name', 'like', trim($name).'%');
        }

        $warns = $query->paginate($perPage);
        
        return ['status' => 1, 'data'=>$warns];
    }
    
    protected function getTypeValue($value) {
        $arr = [
            'sms'=>1,
            'email'=>2,
            'audio'=>4,
        ];

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
       $ret = Warnuser::create($data);
       
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
        $wdefine = Warnuser::findOrFail($id);

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
       $deviceType = Warnuser::findOrFail($id);
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
            'Wu_name' => 'required',
            'Wu_SmsNumber' => 'required',
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
       $deviceType = Warnuser::findOrFail($id);
       if($deviceType->delete()){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
    
    /**
     * 告警设置
     */
    public function warnSetting(Request $request, $uid) {
        
        $data = $request->input('data', []);
        if(!$uid) {
            return ['status' => 0, 'msg'=>'用户参数不存在'];
        }
        
        $user = Warnuser::find($uid);
        if(!$user) {
            return ['status' => 0, 'msg'=>'用户信息不存在'];
        }
        
        $pdiIndex = array_column($data, 'pdi_index');
        
        \App\Warnnotify::where('wu_index', '=', $uid)
                ->whereIn('pdi_index', $pdiIndex)
                ->delete();

        $insertData = [];
        foreach ($data as  $val) {
            $ntype = $this->getNotifyType($val['type']);
            if($ntype < 1) {
                continue;
            }
            $insertData[] = [
                'pdi_index' => $val['pdi_index'],
                'wu_index' => $uid,
                'Wn_notifytype'=> $ntype,
            ];
        }
        // print_r($insertData);
        $rs = DB::table('t_warnnotify')->insert($insertData);
        if($rs) {
            return ['status' => 1, 'msg'=>'保存成功'];
        }else{
            return ['status' => 0, 'msg'=>'保存失败'];
        }
    }
    
    private function getNotifyType($type) {
        $result = [];
        $ntype = config('device.notify_type');
        foreach ($type as $k => $v) {
            if($v){
                $result[] = $ntype[$k];
            }
        }
       return array_sum($result); 
    }
}
