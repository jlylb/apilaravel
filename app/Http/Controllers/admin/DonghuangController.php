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
        $typeIds = array_column($types, 'value');
        $devices = PriDeviceInfo::whereIn('dpt_id', $typeIds)
                ->select(['dpt_id', 'pdi_index'])
                ->get();
        $temp = [];
        if($devices->count()) {
            $temp = $devices->groupBy('dpt_id');
        }
        
        foreach ($types as  $k => $val) {
            $types[$k]['num'] = isset($temp[$k]) ? count($temp[$k]) : 0;
        }
        return ['status'=>1, 'devices'=> array_values($types), 'name'=> array_get(config('dh.names'),$curType)];
    }
    
    /**
     * 获取设备数据
     */
    protected function getDevices($typeId = []) {
        $query = PriDeviceInfo::query();
        if($typeId) {
            $query->whereIn('dpt_id', $typeId);
        }
        $devices = $query
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'pdi_index'])
                ->get()
                ->toArray();

        $deviceItem = null;
        
        $desc = config('dh.desc');
        
        foreach ($devices as  $v) {
            $deviceItem[] = [
                'value' => $v['pdi_index'], 
                'label' => $v['pdi_name'], 
                'icon' => array_get($desc, $v['dpt_id'].'.icon'),
                'areaId' => $v['AreaId'],
                'type' => $v['dpt_id'],
                'router' => array_get($desc, $v['dpt_id'].'.router')
            ];
        }


        return $deviceItem;
    }
    
    /**
     * 根据类型获取设备
     * @param Request $request
     */
    public function device(Request $request) {
        $type = (array)$request -> input('type', []);
        $devices = $this ->getDevices($type);
        return [ 'status' => 1, 'devices' => $devices ];
    }
    /**
     * 动环数据
     */
    public function realData(Request $request) {
        $pdi = $request->input('pdi',0);
        $info = PriDeviceInfo::find($pdi);
        if(!$info) {
            return ['status'=>0, 'msg'=>'设备类型不存在'];
        }  
        $tables = config('dh.desc');
        $curType = $info->dpt_id;
        if(!$curType || !array_has($tables, $curType)) {
            return ['status'=>0, 'msg'=>'设备类型不存在'];
        }
        $table = array_get($tables, $curType.'.realtable');
        
        $devices = DB::table($table)->where('pdi_index', '=', trim($pdi))->first();
        return [ 'status' => 1, 'devices' => $devices ];
    }
}
