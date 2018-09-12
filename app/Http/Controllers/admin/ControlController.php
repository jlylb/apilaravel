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
        $field = $request->input('field', 'rs_status');

        $status = $request->input('value');
        
        $statusInfo = Realstatus::where('pdi_index','=',$pdi)->first();
        if(!$statusInfo) {
            return ['status'=>0, 'msg'=>'设备状态数据不存在'];
        }
        
        $statusInfo->rs_status = $status;
        
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
        $companyId = $request->input('cid');
        $areaId = $request->input('value');
        $device = PriDeviceInfo::where('Co_ID', '=', $companyId)
                ->where('AreaId','=',$areaId)
                ->with(['types'=>function($query){
                    $query->select(['dt_typename', 'dt_typememo', 'dt_typeid']);
                }])
                ->with(['deviceStatus'=>function($query){
                    $query->select(['pdi_index', 'rs_status']);
                }])
//                ->with(['area'=>function($query){
//                    $query->select(['AreaId', 'AreaName', 'Fid', 'Co_ID']);
//                }])
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'Co_ID', 'pdi_index'])
                ->get();

        $types = [];
        $result = [];
        foreach ($device->toArray() as  $v) {
            $v['device_status'] = $v['device_status']?$v['device_status']:['pdi_index'=>$v['pdi_index']];
            $result[$v['AreaId']][$v['dpt_id']][] = $v;
            $types[$v['AreaId']][$v['dpt_id']] = $v['types'];
        }

        return [ 'status'=>1, 'devices' => $result, 'types' => $types ];
    }
}
