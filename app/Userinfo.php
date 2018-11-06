<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userinfo extends Model
{
    protected $table = 'userinfo';
    protected $primaryKey = 'userinfoid';
    protected $fillable = ['nickname', 'useremail', 'userphone', 'Co_ID'];
    protected $hidden = [];
    
    public function User() {
        return $this->belongsTo('\App\SyUser', 'userid', 'userid');
    }
}
