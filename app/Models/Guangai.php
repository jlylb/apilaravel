<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guangai extends Model
{
    protected $table = 't_realdata_guangai';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
