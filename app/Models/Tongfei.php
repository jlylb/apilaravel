<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tongfei extends Model
{
    protected $table = 't_realdata_tongfei';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
