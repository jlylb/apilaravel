<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Air extends Model
{
    protected $table = 't_hisdata_air';
    
    protected $primaryKey = 'hd_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;




    public function device() {
        return $this->hasOne('\App\PriDeviceInfo', 'pdi_index', 'pdi_index');
    }
}
