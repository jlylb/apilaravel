<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    protected $table = 't_devicetype';
    
    protected $primaryKey = 'dt_index';
    
    protected $fillable = ['dt_typeid','dt_typename','dt_issupportext','dt_isenable','dt_rtdata_table','dt_hisdata_table','dt_typememo'];
    
    public $timestamps = false;
}
