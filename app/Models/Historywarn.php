<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historywarn extends Model
{
    use \App\Models\Traits\Company; 
    
    protected $table = 't_historywarn';
    
    protected $primaryKey = 'hw_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
    
    //告警内容
    public function warndefine() {
        return $this->hasOne('\App\WarnDefine', 'wd_index', 'wd_index');
    }
}
