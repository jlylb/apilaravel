<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class SyUser extends Authenticatable implements JWTSubject
{
    use HasRolesAndAbilities, Notifiable;
    
    protected $table = 'sy_user';
    
    protected $primaryKey = 'userid';
    
    protected $fillable = ['userpwd','username','Co_ID'];
    
    protected $hidden = [
        'userpwd', 'remember_token',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function setUserpwdAttribute($value) {
        $this->attributes['userpwd'] = md5($value);
    }
    
    public function company() {
        return $this->belongsTo('\App\Company', 'Co_ID', 'Co_ID');
    }
    
    public function getAuthPassword() {
      return $this->userpwd;
 }
}
