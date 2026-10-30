<?php

namespace App\Models;

class Category extends BaseModel
{
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
