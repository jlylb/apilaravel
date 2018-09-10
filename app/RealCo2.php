<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealCo2 extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_co2';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}
