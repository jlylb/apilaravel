<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\PriDeviceInfo;
use DB;

class DonghuangController extends Controller
{
    /**
     * 动环类型
     */
    public function index(Request $request) {
        $config = config('dh.sys');
        $curType = $request->input('type');
        if(!$curType || !array_has($config, $curType)) {
            return ['status'=>0, 'msg'=>'设备类型不存在'];
        }
        $types = $config[$curType];
        $typeIds = array_column($types, 'rvalue');
        $devices = PriDeviceInfo::whereIn('dpt_id', $typeIds)
                ->select(['dpt_id', 'pdi_index'])
                ->get();
        $temp = [];
        if($devices->count()) {
            $temp = $devices->groupBy('dpt_id');
        }
        $sub = config('dh.subMap');
        foreach ($types as  $k => $val) {
            if(!$temp){
                $types[$k]['num'] = 0;
            }else{
               $types[$k]['num'] = isset($temp[$k]) ? count($temp[$k]) : (isset($temp[$sub[$k]]) ? count($temp[$sub[$k]]) : 0 );
            }
        }
        return ['status'=>1, 'devices'=> array_values($types), 'name'=> array_get(config('dh.names'),$curType)];
    }
    
    /**
     * 获取设备数据
     */
    protected function getDevices($typeId = []) {
        if(!$typeId) {
            return [];
        }
        $query = PriDeviceInfo::query();
        if($typeId) {
            $query->whereIn('dpt_id', $typeId);
        }
        $devices = $query
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'pdi_index'])
                ->with([
                    'types'=>function($query){
                        $query->select(['dt_typeid','haschildType']);
                    },
                ])
                ->get()
                ->toArray();

        $deviceItem = null;
        
        $desc = config('dh.desc');
        
        $map = config('dh.subMap');
        
        foreach ($devices as  $v) {
            $typeId = $v['dpt_id'];
            if(in_array($typeId, $map)) {
                $typeId = $typeId.$v['types']['haschildType'];
            }
            $deviceItem[] = [
                'value' => $v['pdi_index'], 
                'label' => $v['pdi_name'], 
                'icon' => array_get($desc, $typeId.'.icon'),
                'areaId' => $v['AreaId'],
                'type' => $typeId,
                'router' => array_get($desc, $typeId.'.router')
            ];
        }


        return $deviceItem;
    }
    
    /**
     * 根据类型获取设备
     * @param Request $request
     */
    public function device(Request $request) {
        $type = $request -> input('type', []);
        $map = config('dh.subMap');
        if(array_key_exists($type, $map)) {
            $type =  $map[$type];
        }
        $devices = $this ->getDevices((array)$type);
        return [ 'status' => 1, 'devices' => $devices ];
    }
    /**
     * 动环数据
     */
    public function realData(Request $request) {
        $pdi = $request->input('pdi',0);
        $reqType = $request->input('type','');
        $info = PriDeviceInfo::find($pdi);
        if(!$info) {
            return ['status'=>0, 'msg'=>'设备数据不存在'];
        }  
        $tables = config('dh.desc');
        $curType = $info->dpt_id;
        if(!$curType) {
            return ['status'=>0, 'msg'=>'设备类型不存在'];
        }
        $table = array_get($tables, ($reqType?$reqType:$curType).'.realtable');
        
        $devices = DB::table($table)->where('pdi_index', '=', trim($pdi))->first();
        if($curType==33){
            $devices = $this->formatReal($devices, $curType);
        }
        $subField = [];
        if($curType==34 && $devices) {
            $childType = substr($reqType, strlen($curType));
            $sub = $this->getSwitch($curType, $childType, $pdi);
            $sub = array_column($sub, 'tu_SubCha');
            $prefix = 'rd_switch';
            $txtFix = '环境开关量';
            foreach ($sub as $v) {
               $field = $prefix.$v;
               $subField[] = [ 'label'=>$txtFix.$v, 'value'=>!!$devices->$field ];
            }
        }
        return [ 'status' => 1, 'devices' => $devices, 'subFields' => $subField ];
    }
    
    /**
     * 获取开关量
     * @param int $type 主类型
     * @param int $childType 子类型
     */
    protected function getSwitch($type, $childType, $pdi) {
        $table = 't_UserDeSubDev';
        return DB::table($table)->where([
            ['tu_TypeId', '=', $type],
            ['tu_SubTypeId', '=', $childType],
            ['tu_pdi_index', '=', $pdi],
        ])->select(['tu_SubCha'])->get();
    }
    
        /**
     * 格式化实时数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function formatReal($data, $dptId, $prefix = 'rd_') {
        if(!$data) {
            return [];
        }
        $surfix = config('device.surfix')[$dptId];
        $consta = config('device.consta')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit = config('device.units')[$dptId];
        $result = [];
        $data = (array)$data;
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 8;
            $params = [];
            for ($i = 1; $i <= $num; $i++) {
                $param = [];
                foreach ($surfix as $k => $v) {
                    $keyPrefix = $prefix . $k . $i;
                    $keyHwarn = $keyPrefix . 'hwarn';
                    $keyLwarn = $keyPrefix . 'lwarn';
                    $keyPrefix = $prefix . $k . $i;
                    $param[$k] = [
                        $k . '_name' => $v,
                        $k . '_value' => $item->$keyPrefix,
                        'hwarn_name' => $v . $i . '上限状态',
                        'hwarn_value' => $item->$keyHwarn,
                        'lwarn_name' => $v . $i . '下限状态',
                        'lwarn_value' => $item->$keyLwarn,
                    ];
                }
                list($constaField, $constaName) = $consta;
                $constaKey = $prefix . $constaField . $i . 'consta';
                $param['consta'] = array_merge([], [
                    'consta_name' => $constaName,
                    'consta_value' => $item->{$constaKey},
                ]);
                $params[] = $param;
            }
            $result['rd_updatetime'] = $item->rd_updatetime;
            $result['rd_NetCom'] = $item->rd_NetCom;
            $result['pdi_index'] = $item->pdi_index;
            $result['num'] = $num;

            foreach ($surfix as $k => $v) {
                $fields[] = $k;
            }
            // $fields[] = 'consta';
            $result['fields'] = $fields;
            $result['name'] = $desc['name'];
            // $item['params'] = $params;
            $result['items'] = $params;
            $result['unit'] = $unit;

            $result['icons'] = config('device.icons');
        }
        return $result;
    }
    
    /**
     * 保存设备
     * @param Request $request
     * @return array
     */
    public function storeDevice(Request $request)
    {
       $data = $request->input();
       $message = [   
        'pdi_name.required' => '设备名称必须',
        'pdi_name.max' => '设备名称不能超过64个字符',
        
        'pdi_code.required' => '设备编号必须',
        'pdi_code.max' => '设备编号不能超过64个字符',
        
        'dpt_id.required' => '设备分类编号必须',
        'dpt_id.integer' => '设备分类编号必须是数字',
        'dpt_id.exists' => '设备分类不存在',
        ];
        $this->validate($request, [
            'pdi_name' => 'required|max:64',
            'pdi_code' => 'required|max:64',
            'dpt_id' => 'required|integer|exists:t_devicetype,dt_typeid',
        ], $message);
       $data['Co_ID'] = $request->user()->Co_ID;
       $ret = PriDeviceInfo::create($data);
       
       if($ret){
           return ['status' => 1, 'msg'=>'添加成功'];
       }else{
           return ['status' => 0, 'msg'=>'添加失败'];
       }
    }
    
    /**
     * 上传测试
     * @param Request $request
     */
  public function store(Request $request)
 {
 
    $file=$request->file('logo');
    // dd($file);
    $content = file_get_contents($file->getRealPath());
    //文件路径
    $filepath = base_path().'/storage/upload/attach/'.date('YmdHis').'.png';
    //提取base64字符
    // $imgdata = substr($content,strpos($content,",") + 1);
    file_put_contents($filepath,$content );

 }
}
