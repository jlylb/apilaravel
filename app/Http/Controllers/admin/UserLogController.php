<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Pakages\Log\Models\UserLog;

class UserLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $query = UserLog::query();
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('updatetime', $created);
        }

        $logs = $query-> paginate($perPage);
        
        return ['status' => 1, 'data'=>$logs];
    }
}
