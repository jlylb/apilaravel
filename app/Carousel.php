<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $guarded = ['action'];

    public function flashes() {
        return $this->hasMany('\App\Flash','carousel_id');
    }
}
