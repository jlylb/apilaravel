<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealCo2 extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_CO2Concentration';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}
