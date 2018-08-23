<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Area;
/**
 * 监控中心
 *
 * @author litc
 */
class MonitorController extends Controller {
    
    public function index(Request $request) {
        $user = $request->user();
        $companyId = $user->Co_ID;
        $companyId = 1;
        $peng = Area::where('Co_ID', '=', $companyId)
//                ->where('Fid','>','0')
                ->select(['AreaId', 'AreaName', 'Fid', 'Co_ID'])
                ->get()
                ->toArray();
        $province = [];
        $city = [];
        foreach ($peng as $v) {
            if($v['Fid']==0) {
                $province[]=['value'=>$v['AreaId'],'label'=>$v['AreaName']];
            }else{
                $city[$v['Fid']][] = ['value'=>$v['AreaId'],'label'=>$v['AreaName'], 'cid'=>$v['Co_ID']];
            }
        }
        return ['status'=>1, 'province'=>$province, 'city'=>$city];
    }
}
