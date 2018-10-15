<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoilPh extends Model
{
    protected $table = 't_hisdata_SoilPH';
    
    protected $primaryKey = 'hd_index';
    
    public $timestamps = false;
}
