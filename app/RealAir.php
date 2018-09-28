<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealAir extends Model
{
    use \App\Models\device;
    
    protected $table = 't_realdata_envtemphumi';
    
    protected $primaryKey = 'pdi_index';
    
    protected $fillable = [
    
    ];
    
    public $timestamps = false;




}
