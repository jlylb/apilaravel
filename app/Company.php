<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'logo', 'province', 'city', 'district', 'address'
    ];
}
