<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content', 'user_id', 'commentable_id', 'commentable_type','status'
    ];
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->hasone('\App\User');
    }
}
