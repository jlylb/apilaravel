<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiaowen extends Model
{
    protected $table = 't_realdata_tiaowen';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
