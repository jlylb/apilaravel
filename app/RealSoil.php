<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealSoil extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_soil';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}
