<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phpwarn extends Model
{
    protected $table = 't_phpwarn';
    
    protected $primaryKey = 'pdi_index';
    
    protected $guarded = [];
    
    public $timestamps = false;
}
