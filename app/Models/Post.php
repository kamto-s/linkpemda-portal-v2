<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Post extends BaseModel
{
    protected $casts = [
        'type'          => PostType::class,
        'status'        => PostStatus::class,
        'is_featured'   => 'boolean',
        'is_indexable'  => 'boolean',
        'published_at'  => 'datetime',
        'view_count'    => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // scope utk mempermudah filter
    public function scopeArtikel(Builder $query): Builder
    {
        return $query->where('type', PostType::Artikel);
    }

    public function scopeMateri(Builder $query): Builder
    {
        return $query->where('type', PostType::Materi);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published)->where('published_at', '<=', now());
    }
}
