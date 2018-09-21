<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarnDefine extends Model
{
    protected $table = 't_warn_define';
    
    protected $primaryKey = 'wd_index';
    
    protected $guarded = ['isAdd'];
    
    public $timestamps = false;
}
