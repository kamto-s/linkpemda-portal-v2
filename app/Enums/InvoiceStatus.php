<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu ACC',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge-soft-warning',
            self::Approved => 'badge-soft-success',
            self::Rejected => 'badge-soft-danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'mdi-clock-alert-outline',
            self::Approved => 'mdi-check-decagram-outline',
            self::Rejected => 'mdi-close-circle-outline',
        };
    }
}
