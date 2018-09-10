<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Light extends Model
{
    protected $table = 't_hisdata_light';
    
    protected $primaryKey = 'hd_index';
    
    public $timestamps = false;
}
