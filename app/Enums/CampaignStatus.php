<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case Draft = 'draft';
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Queued => 'Dalam Antrean',
            self::Processing => 'Sedang Mengirim',
            self::Completed => 'Selesai Terkirim',
            self::Failed => 'Gagal',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge bg-secondary',
            self::Queued => 'badge bg-info',
            self::Processing => 'badge bg-primary',
            self::Completed => 'badge bg-success',
            self::Failed => 'badge bg-danger',
        };
    }
}

