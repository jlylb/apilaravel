<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealSoilPh extends Model
{
    protected $table = 't_realdata_SoilPH';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}
