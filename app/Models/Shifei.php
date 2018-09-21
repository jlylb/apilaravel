<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shifei extends Model
{
    protected $table = 't_realdata_shifei';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
