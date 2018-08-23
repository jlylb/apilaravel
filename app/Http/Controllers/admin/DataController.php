<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class DataController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('pdi_index', '');
        $table = $request->input('table', '');
        if(empty($table)) {
            return ['status' => 0, 'data'=>[], 'msg'=>'表不存在'];
        }
        $dtType = $request->input('typeid', 0);
        if(empty($dtType)) {
            return ['status' => 0, 'data'=>[], 'msg'=>'设备类型不存在'];
        }
        $query = DB::table($table);
        if(!empty($name)) {
            $query->where('pdi_index',  trim($name));
        }
        $created = $request->input('rd_updatetime', []);
        if(!empty($created)) {
            $query->whereBetween('rd_updatetime', $created);
        }

        $data = $query -> paginate($perPage);
        
        $desc = $this ->getTabelFieldName($dtType);
        

        
        return [ 'status' => 1, 'data'=>$data, 'desc'=>$desc ];
    }
    
    protected function getTabelFieldName($dtTypeid) {
        $table = 't_deviceparam';
        $data = DB::table($table)
                ->where('dt_typeid', '=', $dtTypeid)
                ->select(['dp_paramname', 'dp_paramdesc'])
                ->get();
        return $data;
    }
}
