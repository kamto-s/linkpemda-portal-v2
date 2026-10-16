<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class EmailCampaign extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'body_content',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CampaignStatus::class,
            'sent_at' => 'datetime',
            'total_recipients' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'campaign_id');
    }

    public function contacts(): HasManyThrough
    {
        return $this->hasManyThrough(Contact::class, EmailCampaignRecipient::class, 'campaign_id', 'id', 'id', 'contact_id');
    }
}

