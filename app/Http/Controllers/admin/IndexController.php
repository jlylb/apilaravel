<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Area;
use App\PriDeviceInfo;
use App\Models\Phpwarn;
use DB;

class IndexController extends Controller
{
    
    public function __construct() {

    }

    /**
     * 后台首页
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //大棚数量
        $peng = Area::select(['AreaId', 'AreaName', 'Fid'])
                ->get()
                ->toArray();
        $province = [];
        $city = [];
        foreach ($peng as $v) {
            if ($v['Fid'] == 0) {
                $province[$v['AreaId']] = ['aid' => $v['AreaId'], 'label' => str_replace('省', '', $v['AreaName']) ];
            } else {
                $city[$v['Fid']] = isset($city[$v['Fid']])? $city[$v['Fid']] + 1 : 1;
            }
        }
        $dpTotal = array_sum($city);
        
        foreach ($province as $k => $v) {
            $province[$k]['num'] = (int)$city[$k];
        }
        $dapeng = array_values($province);
        //设备数量
        $deviceTotal = PriDeviceInfo::count();
        //告警数量
        $warnsList = PriDeviceInfo::join('t_phpwarn', 't_prideviceinfo.pdi_index', '=', 't_phpwarn.pdi_devid')
                ->select('pdi_warnlevel', DB::raw('count("t_phpwarn.pdi_index") as num'))
                ->groupBy('pdi_warnlevel')->get();
        
        $warnDesc = config('device.warn_level');
        
//        $warns = $warns->map(function($item) use($warnDesc){ 
//            $item['lvl'] = array_get($warnDesc,$item['pdi_warnlevel'].'.name');
//            return $item;
//        });

        $warns = [];
        $warnTotal = 0;
        if($warnsList) {
            $warnNum = $warnsList->pluck('num', 'pdi_warnlevel')->all();
           
            $warnTotal = array_sum($warnNum);
            foreach ($warnDesc as $k => $v) {
                $warns[]=[ 'lvl'=>$v['name'], 'num'=>isset($warnNum[$k])?$warnNum[$k]:0, 'plvl'=>$k ];
            }
        }
        return compact('dpTotal', 'deviceTotal', 'warnTotal', 'warns', 'dapeng');
    }
}
