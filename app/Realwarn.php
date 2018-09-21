<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Realwarn extends Model
{
    protected $table = 't_realwarn';
    
    protected $primaryKey = 'rw_index';
    
    protected $guarded = ['isAdd'];
    
    public $timestamps = false;
    
    //告警状态
    public function status() {
        return $this->hasOne('\App\Realstatus', 'rw_index', 'rw_index');
    }
    
    //告警内容
    public function warndefine() {
        return $this->hasOne('\App\WarnDefine', 'wd_index', 'wd_index');
    }
    
    public static function getWarnNum($pdiIndex) {
        return static::whereIn('pdi_index', $pdiIndex)
            ->groupBy('pdi_index')
            ->select(['pdi_index', DB::raw('count(rw_index) as warn_num')])
            ->get();
    }
}
