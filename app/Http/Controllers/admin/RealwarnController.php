<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Realwarn;

class RealwarnController extends Controller
{

    
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('pdi_index', '');
        $query = Realwarn::query();
        if(!empty($name)) {
            $query->where('pdi_index', '=', trim($name));
        }
        $query->with([
            'status'=>function($query){
                $query->select(['rw_index', 'rs_status']);
            },
            'warndefine'=>function($query){
                $query->select(['wd_index', 'wd_name', 'wd_warndesc0','wd_warndesc1','wd_level0']);
            },
        ]);
        $warns = $query-> paginate($perPage);
        
        return ['status' => 1, 'data'=>$warns];
    }
}