<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flash extends Model
{
    protected $fillable = [
        'name', 'path', 'desc', 'carousel_id'
    ];
}
