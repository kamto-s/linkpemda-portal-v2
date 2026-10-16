<?php

namespace App\Enums;

enum RecipientStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Sent => 'Terkirim',
            self::Failed => 'Gagal',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge bg-secondary',
            self::Sent => 'badge bg-success',
            self::Failed => 'badge bg-danger',
        };
    }
}

