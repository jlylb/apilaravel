<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $fillable = [
        'name', 'name_en', 'parent_id', 'path'
    ];



    public function flashes() {
        return $this->hasMany('\App\Flash','carousel_id');
    }
}
