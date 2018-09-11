<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Realstatus extends Model
{
    protected $table = 't_realstatus';
    
    protected $primaryKey = 'rs_index';
    
    public $timestamps = false;
    
    protected $fillable = ['rs_status', 'rs_updatetime'];
}
