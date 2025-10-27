<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'age',
        'location',
        'bio',
        'pictures',
        'likes_count',
        'dislikes_count',
    ];

    protected $casts = [
        'pictures' => 'json',
    ];

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'person_id', 'user_id')
            ->where('likes.is_liked', true);
    }
}
