<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PriDeviceInfo;

class VideoController extends Controller
{
    const VIDEO_TYPE = 116;
    
    public function index(Request $request)
    {
        
        $query = PriDeviceInfo::query();
        $query -> where('dpt_id', '=', self::VIDEO_TYPE);
        $query -> select(['pdi_index', 'pdi_name', 'pdi_code']);
        $data = $query -> get();
        
        return [ 'status' => 1, 'data' => $data ];
    }
}
