<?php

namespace App\Contexts\User\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'username',
        'display_name'
    ];

    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'followed_id',
            'follower_id'
        )->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'follower_id',
            'followed_id'
        )->withTimestamps();
    }
}
