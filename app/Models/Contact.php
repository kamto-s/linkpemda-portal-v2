<?php

namespace App\Models;

use App\Enums\ContactCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'organization_name',
        'position',
        'phone',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => ContactCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function campaignRecipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'contact_id');
    }
}

