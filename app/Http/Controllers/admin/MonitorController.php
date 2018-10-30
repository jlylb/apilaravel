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
//        $user = $request->user();
//        $companyId = $user->Co_ID;
//        $companyId = 1;
        $peng = Area::select(['AreaId', 'AreaName', 'Fid', 'Co_ID'])
                ->get()
                ->toArray();
        $province = [];
        $city = [];
        $index = [];
        foreach ($peng as $v) {
            if ($v['Fid'] == 0) {
                $province[] = ['value' => $v['AreaId'], 'label' => $v['AreaName']];
                $index[$v['AreaId']] = 0;
            } else {
                $index[$v['Fid']] += 1; 
                $city[$v['Fid']][] = ['value' => $v['AreaId'], 'label' => $v['AreaName'], 'cid' => $v['Co_ID'], 'alias'=> $index[$v['Fid']].'号大棚'];
            }
        }
        
        $isAreaDevice = $request->input('isArea', true);
        
        $info = [];
        if($isAreaDevice) {
            $info = $this->getAreaDevices();
        }

        return array_merge([ 
            'status' => 1, 
            'province' => $province, 
            'city' => $city, 
        ], $info);
    }

    /**
     * 获取公司所有区域设备
     */
    public function getAreaDevices() {
        $deviceTypeIds = config('device.monitor');
        $devices = PriDeviceInfo::whereIn('dpt_id', array_keys($deviceTypeIds))
                ->with(['types' => function($query) {
                        $query->select(['dt_typename', 'dt_typememo', 'dt_typeid']);
                    }])
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'pdi_index'])
                ->get()
                ->toArray();
        $deviceType = [];
        $device = [];
        $typeIcons = config('device.monitor');
        $areaDevice = [];
        foreach ($devices as $v) {
            $deviceType[$v['AreaId']][$v['dpt_id']] = ['value' => $v['dpt_id'], 'label' => $v['types']['dt_typememo']];
            $device[$v['AreaId']][$v['dpt_id']][] = [
                'value' => $v['pdi_index'], 
                'label' => $v['pdi_name'], 
                'icon' => $typeIcons[$v['dpt_id']],
                'areaId' => $v['AreaId'],
                'device_type' => $v['dpt_id'],
            ];
            $areaDevice[$v['AreaId']][] = [
                'value' => $v['pdi_index'], 
                'label' => $v['pdi_name'], 
                'icon' => $typeIcons[$v['dpt_id']],
                'areaId' => $v['AreaId'],
                'device_type' => $v['dpt_id'],
            ];
        }
        foreach ($deviceType as $k => $v) {
            $deviceType[$k] = array_values($v);
        }
        return compact('deviceType', 'device', 'areaDevice');
    }

    /**
     * 获取一个区域的所有设备
     */
    public function getDevicesByArea(Request $request) {
        $deviceItem = null;

        $areaId = $request->input('areaId');
        if(!$areaId) {
            return $deviceItem;
        }

        $deviceTypeIds = config('device.monitor');

        $devices = PriDeviceInfo::whereIn('dpt_id', array_keys($deviceTypeIds))
                ->where('AreaId', '=', $areaId)
                ->select(['AreaId', 'dpt_id', 'pdi_name', 'pdi_index'])
                ->get()
                ->toArray();

        
        foreach ($devices as  $v) {
            $deviceItem[] = [
                'value' => $v['pdi_index'], 
                'label' => $v['pdi_name'], 
                'icon' => $deviceTypeIds[$v['dpt_id']],
                'areaId' => $v['AreaId'],
                'device_type' => $v['dpt_id'],
            ];
        }

        return ['status' => 1, 'devices' => $deviceItem];
    }

    /**
     * 获取历史数据
     * @param Request $request
     * @return array
     */
    public function deviceData(Request $request) {
        $pdi = $request->input('device');
        $typeId = $request->input('device_type');
        $fmt = $request->input('fmt', 'pc');

        if (!$pdi || !$typeId) {
            return ['status' => 1, 'devices' => []];
        }
        $type = 'history';
        $models = config('device.map_models');
        $model = $models[$typeId][$type];

        if (!$model) {
            return ['status' => 1, 'devices' => []];
        }

        $betweenTime = $this->getTime($request);
        $query = $model::query();

        if ($betweenTime) {
            $query->whereBetween('hd_datetime', $betweenTime);
        }

        $searchType = $this->getSearchType($request);
        list($group, $fields) = $this->getFields($typeId, $searchType);
        $query
                ->where('pdi_index', '=', trim($pdi))
                ->select($fields);
        if ($group) {
            $query->groupBy($group);
        }

        $device = $query->get();
        $result = [];
        if ($device) {
            $method = $fmt=='mobile'?'formatMobile':'format';
            $result = $this->$method($device, $typeId);
        }
        $result['searchType'] = $searchType;
        return ['status' => 1, 'devices' => $result];
    }

    //获取查询方式
    private function getSearchType($request) {
        $seldate = $request->input('selectDate');
        $sdate = $request->input('searchDate');
        $searchType = 'hour';
        if ($seldate) {
            switch ($seldate) {
                case 'day':
                    $searchType = 'hour';
                    break;
                case 'week':
                case 'month':
                    $searchType = 'day';
                    break;
                case 'year':
                    $searchType = 'month';
                    break;
                default:
                    break;
            }
            return $searchType;
        }
        if ($sdate) {
            $zhStart = Carbon::parse($sdate[0]);
            $zhEnd = Carbon::parse($sdate[1]);
            $days = $zhEnd->diffInDays($zhStart);
            if ($days < 1) {
                $searchType = 'hour';
            } elseif ($days >= 1 && $days <= 31) {
                $searchType = 'day';
            } elseif ($days > 31) {
                $searchType = 'month';
            }
        }
        return $searchType;
    }

    /**
     * 获取实时数据
     * @param Request $request
     * @return array
     */
    public function deviceRealData(Request $request) {
        $pdi = $request->input('device');
        $typeId = $request->input('device_type');

        if (!$pdi || !$typeId) {
            return ['status' => 1, 'devices' => []];
        }
        $type = 'real';
        $models = config('device.map_models');
        $model = $models[$typeId][$type];

        if (!$model) {
            return ['status' => 1, 'devices' => []];
        }
        $query = $model::query();
        $device = $query
                ->where('pdi_index', '=', trim($pdi))
                ->get();
        $result = [];
        if ($device) {
            $result = $this->formatReal($device, $typeId);
        }
        $areaId = $request->input('areaId');

        return ['status' => 1, 'devices' => $result];
    }

    /**
     * 返回表字段类型
     * @param int $typeId
     * @return array
     */
    protected function getFields($typeId, $searchType = 'hour', $num = 10, $prefix = 'hd_', $dateField = 'rd_updatetime') {
        $surfix = config('device.surfix');
        $field = $surfix[$typeId];
        $fields = ['pdi_index'];
        foreach ($field as $k => $v) {
            for ($i = 1; $i <= $num; $i++) {
                $key = $prefix . $k . $i;
                $fields[] = $searchType == 'hour' ? $key : DB::raw("round(sum($key)/count($key),2) as $key");
            }
        }
        $group = '';
        switch ($searchType) {
            case 'hour':
                $group = '';
                $fields[] = $dateField;
                break;
            case 'day':
                $group = DB::raw("date_format($dateField, '%Y-%m-%d')");
                $fields[] = DB::raw($group . ' as ' . $dateField);
                break;
            case 'month':
                $group = DB::raw("date_format($dateField, '%Y-%m')");
                $fields[] = DB::raw($group . ' as ' . $dateField);
                break;
            default:
                $group = '';
                $fields[] = $dateField;
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
        // $sdate=['2018-09-28', '2018-09-28'];
        if ($sdate) {
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
     * 格式化实时数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function formatReal($data, $dptId, $prefix = 'rd_') {
        $field = config('device.itemField');
        $surfix = config('device.surfix')[$dptId];
        $consta = config('device.consta')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit = config('device.units')[$dptId];
        $result = [];
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 10;
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

    /**
     * 格式化历史数据
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function format($data, $dptId, $prefix = 'hd_') {
        $surfix = config('device.surfix')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit = config('device.units')[$dptId];
        $result = [];
        $params = [];
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 10;

            for ($i = 1; $i <= $num; $i++) {
                foreach ($surfix as $k => $v) {
                    $keyPrefix = $prefix . $k . $i;
                    $params[$k][$k . $i][$item->rd_updatetime] = $item->$keyPrefix;
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
        $result['icon'] = array_get(config('device.monitor'), $dptId, null);
        return $result;
    }
    
    /**
     * 格式化历史数据手机格式
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function formatMobile($data, $dptId, $prefix = 'hd_') {
        $surfix = config('device.surfix')[$dptId];
        $desc = config('device.desc')[$dptId];
        $numField = $desc['num'];
        $unit = config('device.units')[$dptId];
        $result = [];
        $params = [];
        foreach ($data as $item) {
            $num = $item->{$numField};
            $num = $num ? $num : 10;
            $curParam = [];
            for ($i = 1; $i <= $num; $i++) {
                $temp = [];
                foreach ($surfix as $k => $v) {
                    $keyPrefix = $prefix . $k . $i;
                    $temp[$k] = $item->$keyPrefix;
                }
                $temp['date'] = $item->rd_updatetime;
                $params[$i][] = $temp;
            }
            //$params[] = $curParam;
            $result['pdi_index'] = $item->pdi_index;
            $result['num'] = $num;          
        }
         $result['items'] = $params;
        foreach ($surfix as $k => $v) {
            $fields[] = $k;
        }
        $result['unit'] = $unit;
        $result['fields'] = $fields;
        $result['name'] = $desc['name'];
        $result['surfix'] = $surfix;
        return $result;
    }

}
