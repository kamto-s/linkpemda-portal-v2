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
            self::Draft => 'badge badge-soft-secondary',
            self::Queued => 'badge badge-soft-warning',
            self::Processing => 'badge badge-soft-primary',
            self::Completed => 'badge bg-success-subtle text-success border border-success-subtle',
            self::Failed => 'badge bg-danger-subtle text-danger border border-danger-subtle',
        };
    }
}

