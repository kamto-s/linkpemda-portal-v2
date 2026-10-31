<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    public function getStartedAtAttribute(): ?CarbonInterface
    {
        $firstSent = $this->recipients()->whereNotNull('sent_at')->min('sent_at');
        if ($firstSent) {
            return Carbon::parse($firstSent);
        }

        if ($this->status !== CampaignStatus::Draft && $this->created_at) {
            return $this->created_at;
        }

        return null;
    }

    public function getDeliveryDurationAttribute(): ?string
    {
        $start = $this->recipients()->whereNotNull('sent_at')->min('sent_at');
        if (! $start) {
            if ($this->status !== CampaignStatus::Draft) {
                $start = $this->created_at;
            } else {
                return null;
            }
        }

        $end = $this->sent_at ?? $this->recipients()->whereNotNull('sent_at')->max('sent_at');
        if (! $end) {
            if ($this->status === CampaignStatus::Processing || $this->status === CampaignStatus::Queued) {
                $end = now();
            } else {
                return null;
            }
        }

        $startCarbon = Carbon::parse($start);
        $endCarbon = Carbon::parse($end);

        $diffSeconds = abs($endCarbon->diffInSeconds($startCarbon));

        if ($diffSeconds === 0) {
            if ($this->sent_count > 0 || $this->failed_count > 0) {
                return '< 1 detik';
            }

            return null;
        }

        if ($diffSeconds < 60) {
            $duration = $diffSeconds.' detik';
        } else {
            $days = floor($diffSeconds / 86400);
            $hours = floor(($diffSeconds % 86400) / 3600);
            $minutes = floor(($diffSeconds % 3600) / 60);
            $seconds = $diffSeconds % 60;

            $parts = [];
            if ($days > 0) {
                $parts[] = $days.' hari';
            }
            if ($hours > 0) {
                $parts[] = $hours.' jam';
            }
            if ($minutes > 0) {
                $parts[] = $minutes.' menit';
            }
            if ($seconds > 0) {
                $parts[] = $seconds.' detik';
            }

            $duration = implode(' ', $parts);
        }

        if (! $this->sent_at && ($this->status === CampaignStatus::Processing || $this->status === CampaignStatus::Queued)) {
            $duration .= ' (Sedang Berjalan)';
        }

        return $duration;
    }

    /**
     * Mengambil statistik kuota pengiriman email harian sistem.
     *
     * @return array{limit: int, sent: int, remaining: ?int, percentage: int, is_unlimited: bool}
     */
    public static function getDailyQuotaStats(): array
    {
        $dailyLimit = (int) system_setting('max_daily_email_limit', 0);
        $todaySent = EmailCampaignRecipient::whereDate('sent_at', today())
            ->where('status', \App\Enums\RecipientStatus::Sent)
            ->count();
        $remaining = $dailyLimit > 0 ? max(0, $dailyLimit - $todaySent) : null;
        $percentage = $dailyLimit > 0 ? min(100, (int) round(($todaySent / $dailyLimit) * 100)) : 0;

        return [
            'limit' => $dailyLimit,
            'sent' => $todaySent,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'is_unlimited' => $dailyLimit === 0,
        ];
    }
}
