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
        return ['status'=>1, 'province'=>$province, 'city'=>$city];
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
        $pdi = $request->input('indexs');
        $showType = $request->input('showType', 1);
        
        $table = $request->input('types.dt_hisdata_table', '');
        if(empty($table)) {
            return [ 'status'=>0, 'msg'=>'数据表不存在' ];
        }
        $typeId = $request->input('dpt_id');
        $fields = $this->getFields($typeId, $table);
        $betweenTime = $this->getTime($request);
        $query = DB::query();
        if($betweenTime) {
            $query->whereBetween('hd_datetime', $betweenTime);
        }
        if($showType==2) {
            $device = $query->from($table)
                ->whereIn('pdi_index', explode(',', trim($pdi)))
                ->select($fields)
                ->paginate($perPage);
            return [ 'status'=>1, 'data'=>$device, 'labels'=> $this->getFieldsDesc($table) ];
        } else {
            $device = $query->from($table)
                ->whereIn('pdi_index', explode(',', trim($pdi)))
                ->select($fields)
                ->get();
            $result = [];
            if($device) {
                $result = $this->format($device, $table);
            }
            return [ 'status'=>1, 'devices'=>$result ];
        }
    }
    
    /**
     * 获取实时数据
     * @param Request $request
     * @return array
     */
    public function deviceRealData(Request $request) {
        $pdi = $request->input('indexs');
        $table = trim($request->input('types.dt_rtdata_table', ''));
        if(empty($table)) {
            return [ 'status'=>0, 'msg'=>'数据表不存在' ];
        }
        $table = strtolower($table);
        $typeId = $request->input('dpt_id');
        $fields = ['*'];
        $model = '';
        if(isset($this->mapModels()[$table])){
            $model = $this->mapModels()[$table];
        }

        if(!$model) {
            return [ 'status'=>1, 'devices'=>[] ];
        }
        $query = $model::query();
        $device = $query->from($table)
                ->whereIn('pdi_index', explode(',', trim($pdi)))
                ->select($fields)
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
    protected function getFields($typeId, $table) {
        $fieldsArr = [
            1 => ['pdi_index', 'hd_upscurr', 'hd_datetime'],
        ];
        return isset($fieldsArr[$typeId])?$fieldsArr[$typeId]:['*'];
    }
    
    /**
     * 获取时间范围
     * @param Request $request
     * @return array
     */
    protected function getTime(Request $request) {
        $seldate = $request->input('selectDate');
        $sdate = $request->input('searchDate');
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
        $numField = config('device.num')[$dptId];
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
                        $k.'_name'=>$v.$i, 
                        $k.'_value'=>$item->$keyPrefix.' '.$unit[$k],
                        'hwarn_name'=>'上限状态',
                        'hwarn_value'=>$item->$keyHwarn,
                        'lwarn_name'=>'下限状态',
                        'lwarn_value'=>$item->$keyLwarn,
                    ];
                }
                list($constaField, $constaName) = $consta;
                $constaKey = $prefix.$constaField.$i.'consta';
                $param['consta'] = [
                    'consta_name'=>$constaName, 
                    'consta_value'=>$item->{$constaKey},
                ];
                $params[] = $param;               
            }
            $result['rd_updatetime'] = $item->rd_updatetime;
            $result['rd_NetCom'] = $item->rd_NetCom;
            $result['pdi_index'] = $item->pdi_index;
            
            foreach ($surfix as $k => $v) {
                $fields[] = $k;
            }
            $fields[] = 'consta';
            $result['fields'] = $fields;
            // $item['params'] = $params;
            $result['items'] = $params;
        }
        return $result;
    }
    
    /**
     * 格式化历史数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function format($data, $table) {
        $map = $this->mapFields();
        $field = $map[$table];
        $result = [];
        foreach ($data as $item) {
            foreach ($field as $k => $v) {
                $result[$item->pdi_index][$k][] = [strtotime($item->hd_datetime)*1000, $item->{$v}];
            }
        }
        return $result;
    }
}
