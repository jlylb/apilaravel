<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Co2 extends Model
{
    protected $table = 't_hisdata_co2concentration';
    
    protected $primaryKey = 'hd_index';
    
    public $timestamps = false;
}
