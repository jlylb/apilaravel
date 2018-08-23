<?php

namespace App\Pakages\Log\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $table = 'user_log';
    
    public $timestamps = false;
    
    protected $fillable = ['userid','username','type','ip','content','guard'];
}
