<?php

namespace App\Enums;

enum InvoicePaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Lunas',
            self::Partial => 'DP (Sebagian)',
            self::Paid => 'Lunas',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Unpaid => 'badge-soft-danger',
            self::Partial => 'badge-soft-warning',
            self::Paid => 'badge-soft-success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Unpaid => 'mdi-cash-remove',
            self::Partial => 'mdi-cash-clock',
            self::Paid => 'mdi-cash-check',
        };
    }
}

