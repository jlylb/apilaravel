<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Area;
use App\PriDeviceInfo;
use DB;
use Carbon\Carbon;
/**
 * 监控中心
 *
 * @author litc
 */
class MonitorController extends Controller {
    
    /**
     * 获取大棚
     * @param Request $request
     * @return array
     */
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
        
//        $deviceDesc = config('device.desc');
//        $deviceType = [];
//        foreach ($deviceDesc as $k => $v) {
//            $deviceType[] = [ 'value' => $k, 'label' => $v['name'] ];
//        }
        $info = $this -> getAreaDevices();
        return ['status'=>1, 'province'=>$province, 'city'=>$city, 'deviceType' => $info['deviceType'], 'device' => $info['device']];
    }
    
    /**
     * 获取公司所有区域设备
     */
    public function getAreaDevices() {
        $deviceTypeIds = config('device.monitor'); 
        $devices = PriDeviceInfo::whereIn('dpt_id', array_keys($deviceTypeIds))
        ->with(['types'=>function($query){
            $query->select(['dt_typename', 'dt_typememo', 'dt_typeid']);
        }])
        ->select(['AreaId', 'dpt_id', 'pdi_name', 'pdi_index'])
        ->get()
        ->toArray();
        $deviceType = [];
        $device = [];
        foreach ($devices as $v) {
            $deviceType[$v['AreaId']][$v['dpt_id']] = [ 'value' => $v['dpt_id'], 'label' => $v['types']['dt_typememo'] ];
            $device[$v['AreaId']][$v['dpt_id']][] = [ 'value' => $v['pdi_index'], 'label' => $v['pdi_name'] ];
        }
        foreach ($deviceType as $k => $v) {
            $deviceType[$k] = array_values($v);
        }
        return compact('deviceType', 'device');
    }
    
    /**
     * 获取设备
     * @param Request $request
     * @return array
     */
    public function device(Request $request) {
        $companyId = $request->input('cid');
        $areaId = $request->input('value');
        $deviceTypeIds = config('device.monitor'); 
        $device = PriDeviceInfo::where('Co_ID', '=', $companyId)
                ->where('AreaId','=',$areaId)
                ->whereIn('dpt_id', array_keys($deviceTypeIds))
                ->with(['types'=>function($query){
                    $query->select(['dt_typename', 'dt_typememo', 'dt_hisdata_table', 'dt_rtdata_table', 'dt_typeid']);
                }])
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'Co_ID',DB::raw('group_concat(pdi_index) as indexs')])
                ->groupBy('dpt_id')
                ->get()
                ->toArray();

        return [ 'status'=>1, 'devices'=>$device, 'icon'=>$deviceTypeIds ];
    }
    
    /**
     * 获取历史数据
     * @param Request $request
     * @return array
     */
    public function deviceData(Request $request) {
        
        $perPage = $request->input('pageSize',15);
        $showType = $request->input('showType', 1);
        
        $pdi = $request->input('device');
        $typeId = $request->input('device_type');

        if(!$pdi||!$typeId) {
            return [ 'status'=>1, 'devices'=>[] ];
        }
        $type = 'history';
        $models = config('device.map_models');
        $model = $models[$typeId][$type];

        if(!$model) {
            return [ 'status'=>1, 'devices'=>[] ];
        }

        $betweenTime = $this->getTime($request);
        $query = $model::query();

        if($betweenTime) {
            $query->whereBetween('rd_updatetime', $betweenTime);
        }
        $searchType = 'day';
        list($group, $fields) = $this->getFields($typeId,$searchType);
        $query
            ->where('pdi_index', '=', trim($pdi))
            ->select($fields);
        if($group){
            $query ->groupBy($group);
        }
           
        $device =   $query  ->get();
        $result = [];
        if($device) {
            $result = $this->format($device, $typeId);
        }
        $result['searchType']=$searchType;
        return [ 'status'=>1, 'devices'=>$result ];
        
    }
    
    /**
     * 获取实时数据
     * @param Request $request
     * @return array
     */
    public function deviceRealData(Request $request) {
        $pdi = $request->input('device');
        $typeId = $request->input('device_type');

        if(!$pdi||!$typeId) {
            return [ 'status'=>1, 'devices'=>[] ];
        }
        $type = 'real';
        $models = config('device.map_models');
        $model = $models[$typeId][$type];

        if(!$model) {
            return [ 'status'=>1, 'devices'=>[] ];
        }
        $query = $model::query();
        $device = $query
                ->where('pdi_index', '=', trim($pdi))
                ->get();
        $result = [];
        if($device) {
            $result = $this->formatReal($device, $typeId);
        }

        return [ 'status'=>1, 'devices'=>$result ];
    }
    
    
    /**
     * 返回表字段类型
     * @param int $typeId
     * @return array
     */
    protected function getFields($typeId, $searchType='day', $num=10, $prefix='hd_', $dateField='rd_updatetime') {
        $surfix = config('device.surfix');
        $field = $surfix[$typeId];
        $fields = ['pdi_index'];
        foreach($field as $k => $v) {
            for($i = 1; $i <= $num; $i++) {
                $key = $prefix.$k.$i;
                $fields[]=$searchType=='day' ? $key : DB::raw("round(sum($key)/count($key),2) as $key");
            }
        }
        $group = '';
        switch ($searchType) {
            case 'day':
                $group = '';
                $fields[]=$dateField;
                break;
            case 'week':
            case 'month':
                $group = DB::raw("date($dateField)");
                $fields[]=DB::raw($group. ' as '.$dateField);
                break;
            case 'year':
                $group = DB::raw("month($dateField)");
                $fields[]=DB::raw($group. ' as '.$dateField);
                break;            
            default:
                $group = '';
                $fields[]=$dateField;
                break;
        }
        return [$group, $fields];

    }
    
    /**
     * 获取时间范围
     * @param Request $request
     * @return array
     */
    protected function getTime(Request $request) {
        $seldate = $request->input('selectDate');
        $sdate = $request->input('searchDate');
        $sdate=['2018-09-28', '2018-09-28'];
        if($sdate) {
            $zhStart = Carbon::parse($sdate[0]);
            $zhEnd = Carbon::parse($sdate[1]);
            return [$zhStart->startOfDay()->toDateTimeString(), $zhEnd->endOfDay()->toDateTimeString()];
        }
        $zhStart = Carbon::now();
        $zhEnd = Carbon::now();
        $date = [];
        switch ($seldate) {
            case 'day':
                $date = [$zhStart->startOfDay()->toDateTimeString(), $zhEnd->endOfDay()->toDateTimeString()];
                break;
            case 'week':
                $start = $zhStart->startOfWeek(Carbon::MONDAY);
                $end = $zhEnd->endOfWeek(Carbon::SUNDAY);
                $date = [$start->toDateTimeString(), $end->toDateTimeString()];
                break;
            case 'month':
                $date = [$zhStart->startOfMonth()->toDateTimeString(), $zhEnd->endOfMonth()->toDateTimeString()];
                break;
            case 'year':
                $date = [$zhStart->startOfYear()->toDateTimeString(), $zhEnd->endOfYear()->toDateTimeString()];
                break;
            default:
                break;
        }
        return $date;
    }
    
    /**
     * 表映射字段
     * @return array
     */
    protected function mapFields() {
        return [
            't_hisdata_air' => ['temp'=>'hd_temp', 'wet'=>'hd_wet'],
            't_hisdata_liquid' => ['liquid'=>'hd_level'],
            't_hisdata_soil' => ['temp'=>'hd_temp', 'salt'=>'hd_salt'],
            't_hisdata_co2' => ['co2'=>'hd_co2_concentration'],
            't_hisdata_light' => ['light'=>'hd_light_intensity'],
            
            't_realdata_air' => ['rd_temp'=>'温度', 'rd_wet'=>'湿度'],
            't_realdata_liquid' => ['rd_level'=>'水位'],
            't_realdata_soil' => ['rd_temp'=>'温度', 'rd_salt'=>'湿度'],
            't_realdata_co2' => ['rd_co2_concentration'=>'浓度'],
            't_realdata_light' => ['rd_light_intensity'=>'光照度'],
        ];
    }
    

    /**
     * 表对应模型
     * @return type
     */
    protected function mapModels() {
        return [
            't_hisdata_envtemphumi' => '\App\Air',
            't_hisdata_levelvalue' => '\App\Liquid',
            't_hisdata_soilcondth' => '\App\Soil',
            't_hisdata_co2concentration' => '\App\Co2',
            't_hisdata_lightintensity' => '\App\Light',
            't_realdata_envtemphumi' => '\App\RealAir',
            't_realdata_levelvalue' => '\App\RealLiquid',
            't_realdata_soilcondth' => '\App\RealSoil',
            't_realdata_co2concentration' => '\App\RealCo2',
            't_realdata_lightintensity' => '\App\RealLight',
        ];
    }
    
    /**
     * 表对应字段描述
     * @return type
     */
    protected function getFieldsDesc($table) {
        $desc = [
            't_hisdata_air' => ['hd_index'=>'编号','rd_updatetime'=>'更新时间','hd_datetime'=>'创建时间','pdi_index'=>'设备编号','hd_temp'=>'温度', 'hd_wet'=>'湿度'],
            't_hisdata_liquid' => ['hd_index'=>'编号','rd_updatetime'=>'更新时间','hd_datetime'=>'创建时间','pdi_index'=>'设备编号','hd_level'=>'液位'],
            't_hisdata_soil' => ['hd_index'=>'编号','rd_updatetime'=>'更新时间','hd_datetime'=>'创建时间','pdi_index'=>'设备编号','hd_temp'=>'温度', 'hd_salt'=>'盐碱度'],
            't_hisdata_co2' => ['hd_index'=>'编号','rd_updatetime'=>'更新时间','hd_datetime'=>'创建时间','pdi_index'=>'设备编号','hd_co2_concentration'=>'浓度'],
            't_hisdata_light' => ['hd_index'=>'编号','rd_updatetime'=>'更新时间','hd_datetime'=>'创建时间','pdi_index'=>'设备编号','hd_light_intensity'=>'光照度'],
            
            't_realdata_air' => [ 'rd_updatetime'=>'更新时间', 'pdi_index'=>'设备编号', 'rd_temp'=>'温度', 'rd_wet'=>'湿度'],
            't_realdata_liquid' => [ 'rd_updatetime'=>'更新时间', 'pdi_index'=>'设备编号', 'rd_level'=>'液位'],
            't_realdata_soil' => [ 'rd_updatetime'=>'更新时间', 'pdi_index'=>'设备编号', 'rd_temp'=>'温度', 'rd_salt'=>'盐碱度'],
            't_realdata_co2' => ['rd_updatetime'=>'更新时间', 'pdi_index'=>'设备编号', 'rd_co2_concentration'=>'浓度'],
            't_realdata_light' => [ 'rd_updatetime'=>'更新时间', 'pdi_index'=>'设备编号', 'rd_light_intensity'=>'光照度'],
        ];
        return $desc[$table];
    }
    
    /**
     * 格式化实时数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function formatReal($data, $dptId, $prefix='rd_') {
        $field = config('device.itemField');
        $surfix=config('device.surfix')[$dptId];
        $consta = config('device.consta')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit =  config('device.units')[$dptId];
        $result = [];
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 10;
            $params = [];
            for($i = 1; $i <= $num; $i++) {
                $param = [];
                foreach ($surfix as $k => $v) {
                    $keyPrefix=$prefix.$k.$i;
                    $keyHwarn=$keyPrefix.'hwarn';
                    $keyLwarn=$keyPrefix.'lwarn';
                    $keyPrefix=$prefix.$k.$i;
                    $param[$k] = [ 
                        $k.'_name'=>$v, 
                        $k.'_value'=>$item->$keyPrefix,
                        'hwarn_name'=>$v.$i.'上限状态',
                        'hwarn_value'=>$item->$keyHwarn,
                        'lwarn_name'=>$v.$i.'下限状态',
                        'lwarn_value'=>$item->$keyLwarn,
                    ];
                }
                list($constaField, $constaName) = $consta;
                $constaKey = $prefix.$constaField.$i.'consta';
                $param['consta'] = array_merge([],[
                    'consta_name'=>$constaName, 
                    'consta_value'=>$item->{$constaKey},
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
     * 格式化历史数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function format($data, $dptId, $prefix='hd_') {
        $surfix=config('device.surfix')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit =  config('device.units')[$dptId];
        $result = [];
        $params = [];
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 10;
            
            for($i = 1; $i <= $num; $i++) {
                foreach ($surfix as $k => $v) {
                    $keyPrefix=$prefix.$k.$i;
                    $params[$k][$k.$i][$item->rd_updatetime] = $item->$keyPrefix;
                }             
            }
            $result['pdi_index'] = $item->pdi_index;
            $result['num'] = $num;
            $result['items'] = $params;

        }
        foreach ($surfix as $k => $v) {
            $fields[] = $k;
        }
        $result['unit'] = $unit;
        $result['fields'] = $fields;
        $result['name'] = $desc['name'];
        $result['surfix'] = $surfix;
        return $result;
    }

    protected function getDays($searchType) {


    }
}
