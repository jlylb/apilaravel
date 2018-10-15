<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\Phpwarn;

class RealwarnController extends Controller
{

    
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        
        $name = $request->input('pdi_index', '');
        
        $query = Phpwarn::query();
        
        if(!empty($name)) {
            $query->where('pdi_devid', '=', trim($name));
        }
        
        $lvl = $request->input('lvl', '');
        
        if(!empty($lvl)) {
            $query->where('pdi_warnlevel', '=', trim($lvl));
        }
        
//        $query->with([
//            'status'=>function($query){
//                $query->select(['rw_index', 'rs_status']);
//            },
//            'warndefine'=>function($query)use($lvl){
//                if(!empty($lvl)) {
//                    $query->where('wd_level0', '=', trim($lvl));
//                }
//                $query->select(['wd_index', 'wd_name', 'wd_warndesc0','wd_warndesc1','wd_level0']);
//            },
//        ]);
        $warns = $query-> paginate($perPage);
        
        return ['status' => 1, 'data'=>$warns, 'icons'=>config('device.monitor'), 'wlevel'=>config('device.warn_level')];
    }
}
