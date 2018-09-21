<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juanlian extends Model
{
    protected $table = 't_realdata_juanlian';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
