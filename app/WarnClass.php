<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarnClass extends Model
{
    protected $table = 't_warnclass';
    
    protected $primaryKey = 'wc_index';
    
    protected $fillable = ['wc_index', 'Wc_classname', 'Wc_memo'];
    
    public $timestamps = false;
}
