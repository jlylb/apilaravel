<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\PriDeviceInfo;

use App\Realstatus;

/**
 * 控制管理 
 */
class ControlController extends Controller
{
    use \App\Http\traits\UserPrivilege;
    
    /**
     * 更新设备状态
     * @param Request $request
     * @param int $pdi
     * @return array
     */
    public function update(Request $request, $pdi) {
        $user = $this->user();
        $cid = $user->Co_ID;
        $where = [
            ['pdi_index', '=', $pdi],
            ['Co_ID', '=', $cid],
        ];
        $isExist = PriDeviceInfo::where($where)->count();
        if(!$isExist) {
            return ['status'=>0, 'msg'=>'该公司不存在此设备'];
        }

        $status = $request->input('value');
        $field = $request->input('field');
        $dptId = $request->input('dpt_id');
        
        $models = config('device.models');
        if(!array_key_exists($dptId, $models)){
             return ['status'=>0, 'msg'=>'设备类型不存在'];
        }
        $model = $models[$dptId];

        $statusInfo = $model::where('pdi_index','=',$pdi)->first();
        if(!$statusInfo) {
            return ['status'=>0, 'msg'=>'设备状态数据不存在'];
        }
        
        $statusInfo->$field = $status;
        
        if($statusInfo->save()) {
            return ['status'=>1, 'msg'=>'状态保存成功'];
        }else{
            return ['status'=>0, 'msg'=>'状态保存失败'];
        }
    }
    
    /**
     * 获取设备
     * @param Request $request
     * @return array
     */
    public function device(Request $request) {
        $user = $this->user();
        $areaId = $request->input('value');
        $deviceTypeIds = config('device.control');
        $device = PriDeviceInfo::where('AreaId','=',$areaId)
                ->with(['types'=>function($query){
                    $query->select(['dt_typename', 'dt_typememo', 'dt_typeid']);
                }])
                ->whereIn('dpt_id', array_keys($deviceTypeIds))
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'Co_ID', 'pdi_index'])
                ->get();
        $types = [];
        $result = [];
        foreach ($device->toArray() as  $v) {
            $result[$v['AreaId']][$v['dpt_id']][] = $v;
            $types[$v['AreaId']][$v['dpt_id']] = $v['types'];
        }
        return [ 'status'=>1, 'devices' => $result, 'types' => $types, 'icon'=>$deviceTypeIds ];
    }
    
    /**
     * 获取设备参数
     * @param Request $request
     * @return array
     */
    public function deviceData(Request $request) {
        $dptId = $request->input('dpt_id');
        $models = config('device.models');
        if(!array_key_exists($dptId, $models)){
             return ['status'=>0, 'msg'=>'设备类型不存在'];
        }
        $model = $models[$dptId];
        $pdiIndex = $request->input('pdi_index');
        $data = $model::whereIn('pdi_index', $pdiIndex)
                ->select(['pdi_index', 'device_status', 'running_status'])
                ->get()->keyBy('pdi_index')->toArray();
        if(!$data) {
            $data = [];
            foreach ($pdiIndex as $index) {
                $data[$index]=['device_status'=>1, 'running_status'=>1];
            }
        }
        return [ 'status'=>1, 'devicesData' => $data ];
    }
}
