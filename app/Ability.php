<?php

namespace App;

use Silber\Bouncer\Database\Ability as BaseAbility;

class Ability extends BaseAbility
{
    protected $fillable = [
        'name', 'title', 'route_path'
    ];
}
