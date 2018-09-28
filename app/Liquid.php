<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Liquid extends Model
{
    protected $table = 't_hisdata_levelvalue';
    
    protected $primaryKey = 'hd_index';
    
    public $timestamps = false;
}
