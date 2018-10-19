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
        if($curType==33){
            $devices = $this->formatReal([$devices], $curType);
        }
        return [ 'status' => 1, 'devices' => $devices ];
    }
    
        /**
     * 格式化实时数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function formatReal($data, $dptId, $prefix = 'rd_') {
        $surfix = config('device.surfix')[$dptId];
        $consta = config('device.consta')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit = config('device.units')[$dptId];
        $result = [];
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
}
