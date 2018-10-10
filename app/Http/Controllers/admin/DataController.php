<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Http\traits\Utils;

class DataController extends Controller
{
    use Utils;
    
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
        $columns = $this -> getColumnFromTable($table);

        $data = $query -> paginate($perPage);
        
        $desc = $this ->getTabelFieldName($dtType);
        
        $prefix = strpos($table,'realdata')!==false?'rd_':'hd_';
        
        $fieldDesc = $this->formatFields($columns, $desc, $prefix);
        
        return [ 'status' => 1, 'data'=>$data, 'desc'=>$fieldDesc ];
    }
    
    protected function getTabelFieldName($dtTypeid) {
        $table = 't_deviceparam';
        $data = DB::table($table)
                ->where('dt_typeid', '=', $dtTypeid)
                ->select(['dp_paramname', 'dp_paramdesc'])
                ->get();
        if($data) {
            $data = $data -> pluck('dp_paramdesc', 'dp_paramname');
        }
        return $data;
    }
    
    /**
     * 格式化字段描述
     * @param array $columns
     * @param array $desc
     * @param string $prefix
     * @return array
     */
    private function formatFields($columns, $desc, $prefix) {
        $except = [
            'pdi_index' => [ 'label'=>'设备索引' ],
            'rd_updatetime' => [ 'label'=>'更新时间' ]
        ];
        if(!$desc) {
            return [];
        }
        $arr = [];
        foreach ($columns as $val) {
            if(array_key_exists($val, $except)) {
                $arr[$val] = $except[$val];
            }else{
                $key = str_replace($prefix, '', $val);
                $arr[$val] = [ 'label'=>$desc[$key] ];
            }
        }
        return $arr;
    }
}
