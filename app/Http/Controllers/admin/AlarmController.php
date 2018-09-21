<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\PriDeviceInfo;

class AlarmController extends Controller
{
    use \App\Http\traits\UserPrivilege;
    /**
     * 获取设备
     * @param Request $request
     * @return array
     */
    public function device(Request $request) {
        // $companyId = $request->input('cid');
        $user = $this->user();
        $companyId = $user->Co_ID;
        $areaId = $request->input('value');
        $deviceTypeIds = config('device.monitor');
        $device = PriDeviceInfo::where('Co_ID', '=', $companyId)
                ->where('AreaId','=',$areaId)
                ->with(['types'=>function($query){
                    $query->select(['dt_typename', 'dt_typememo', 'dt_typeid']);
                }])
                ->with(['deviceStatus'=>function($query){
                    $query->select(['pdi_index', 'rs_status']);
                }])
                ->whereIn('dpt_id', array_keys($deviceTypeIds))
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'Co_ID', 'pdi_index'])
                ->get();

        $types = [];
        $result = [];
        $pdiIndexs = $device->pluck('pdi_index')->toArray();
        $warnNum = \App\Realwarn::getWarnNum($pdiIndexs)->pluck('warn_num', 'pdi_index')->toArray();
        foreach ($device->toArray() as  $v) {
            $v['device_status'] = $v['device_status']?$v['device_status']:['pdi_index'=>$v['pdi_index']];
            $v['warn_num'] = ($warnNum && isset($warnNum[$v['pdi_index']]))?$warnNum[$v['pdi_index']]:0;
            $result[$v['AreaId']][$v['dpt_id']][] = $v;
            $types[$v['AreaId']][$v['dpt_id']] = $v['types'];
        }

        return [ 'status'=>1, 'devices' => $result, 'types' => $types, 'icon'=>$deviceTypeIds ];
    }
}
