<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriDeviceInfo extends Model
{
    protected $table = 't_prideviceinfo';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = ['pdi_name', 'pdi_code', 'dpt_id', 'Co_ID', 'AreaId'];
    
    public $timestamps = false;
    
    public function company() {
        return $this->hasOne('\App\Company', 'Co_ID', 'Co_ID');
    }
    
    public function area() {
        return $this->hasOne('\App\Area', 'AreaId', 'AreaId');
    }
    
}
