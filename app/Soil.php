<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soil extends Model
{
    protected $table = 't_hisdata_soilcondth';
    
    protected $primaryKey = 'hd_index';
    
    public $timestamps = false;
}
