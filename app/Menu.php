<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'route_path', 'route_name', 'component', 'redirect', 'meta', 'pid'
    ];
    
    public function setMetaAttribute($value) {
        $this->attributes['meta'] = json_encode($value);
    }
    
//    public function getMetaAttribute($value) {
//         return json_decode($value);
//    }
}
