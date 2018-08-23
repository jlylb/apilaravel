<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 't_companycnfo';
    
    protected $primaryKey = 'Co_ID';
    
    protected $fillable = [
        'Co_Name', 'Co_Logo', 'Co_ConnectionsNumber'
    ];
    
    
    public function areas() {
        return $this->hasMany('\App\Area', 'Co_ID', 'Co_ID');
    }
}
