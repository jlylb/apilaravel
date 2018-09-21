<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buguang extends Model
{
    protected $table = 't_realdata_buguang';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;
}
