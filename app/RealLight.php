<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealLight extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_lightintensity';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}
