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
        $rsStatus = $request->input('rs_status');
        
        $statusInfo = Realstatus::where('pdi_index','=',$pdi)->first();
      
        $statusInfo->rs_status = $rsStatus;
        if($statusInfo->save()) {
            return ['status'=>1, 'msg'=>'状态保存成功'];
        }else{
            return ['status'=>0, 'msg'=>'状态保存失败'];
        }
    }
}
