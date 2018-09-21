<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealLiquid extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_liquid';
    
    protected $primaryKey = 'pdi_index';
    
    public $timestamps = false;
}