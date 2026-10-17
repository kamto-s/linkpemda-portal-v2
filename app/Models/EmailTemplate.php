<?php

namespace App\Models;

use App\Enums\EmailTemplateCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'subject',
        'body_content',
        'is_active',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'category' => EmailTemplateCategory::class,
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->latest();
    }

    public function scopeByCategory(Builder $query, string|EmailTemplateCategory $category): Builder
    {
        $val = $category instanceof EmailTemplateCategory ? $category->value : $category;
        return $query->where('category', $val);
    }
}
