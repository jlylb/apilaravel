<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Historywarn;
use App\PriDeviceInfo;
use DB;
use Carbon\Carbon;

class ReportController extends Controller {

    protected $message = [
        
    ];

    /**
     * 历史告警统计
     *
     * @return array
     */
    public function index(Request $request) {
        
        $perPage = $request->input('pageSize', 15);

        $query = Historywarn::query();


        $pdiIndex = $request->input('pdi_index', '');

        if (!empty($pdiIndex)) {
            $query->where('pdi_index', '=', trim($pdiIndex));
        }

        $wdIndex = $request->input('wd_index', '');
        if (!empty($wdIndex)) {
            $query->where('wd_index', '=', trim($wdIndex));
        }

        $createdAt = $request->input('rs_updatetime', '');
        if (!empty($createdAt)) {
            $query->whereBetween('rs_updatetime', $createdAt);
        }
        $lvl = $request->input('lvl', '');
        if (!empty($lvl)) {
            $query->where('wd_level0', '=', trim($lvl));
        }
        $query
            ->leftJoin('t_warn_define', 't_warn_define.wd_index', '=', 't_historywarn.wd_index')
            ->select(['t_historywarn.*', 'wd_level0']);

        $warns = $query->paginate($perPage);

        return ['status' => 1, 'data' => $warns];
    }
    
    /**
     * 历史统计
     * @param Request $request
     * @return array
     */
    public function historysum(Request $request) {

        $query = Historywarn::query();

        $pdiIndex = $request->input('pdi_index', '');

        if (!empty($pdiIndex)) {
            $query->where('pdi_index', '=', trim($pdiIndex));
        }


        $createdAt = $request->input('rs_updatetime', '');
        $dates = $this->getTime($createdAt, ['6 months ago', 'today']);

        if (!empty($dates)) {
            $query->whereBetween('rs_updatetime', $dates);
        }

        $query
            ->groupBy(['pdi_index', 'wd_level0'])
            ->leftJoin('t_warn_define', 't_warn_define.wd_index', '=', 't_historywarn.wd_index')
            ->select(['pdi_index', 't_historywarn.wd_index', 'pdi_name as name', 'wd_level0', DB::raw('count(t_historywarn.wd_index) as value')]);



        $warns = $query->get();

        return ['status' => 1, 'data' => $warns, 'rs_updatetime' => $dates];
    }
    
    /**
     * 资产统计
     * @param Request $request
     * @return array
     */
    public function assetsum(Request $request) {

        $query = PriDeviceInfo::query();

        $query
            ->leftJoin('t_devicetype', 't_devicetype.dt_typeid', '=', 't_prideviceinfo.dpt_id')
            ->select(['pdi_index', 'dpt_id', 'pdi_name', 'dt_typename', 'dt_typememo']);

        $pdi = $query->get();

        $result = [];

        $group = $pdi->groupBy('dpt_id');
        $pdiType = $pdi->pluck('dt_typememo', 'dpt_id');

        foreach ($pdiType as $key => $val) {
            $result[] = ['value' => count($group[$key]), 'name' => $val, 'device' => $group[$key], 'dpt_id' => $key];
        }

        return ['status' => 1, 'data' => $result, 'total' => $pdi->count()];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
    }

    protected function validateData($request) {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
    }
    
    /**
     * 获取时间
     * @param type $dates
     * @param type $defaults
     * @return array
     */
    protected function getTime($dates, $defaults = ['7 days ago', 'today']) {

        if (!$dates) {
            $dates = $defaults;
        }

        list($startDay, $endDay) = $dates;

        $zhStart = Carbon::parse($startDay)->startOfDay()->toDateTimeString();
        $zhEnd = Carbon::parse($endDay)->endOfDay()->toDateTimeString();
        return [$zhStart, $zhEnd];
    }

}
