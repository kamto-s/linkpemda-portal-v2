<?php

namespace App\Enums;

enum ContactCategory: string
{
    case Pemerintah = 'pemerintah';
    case Swasta = 'swasta';

    public function label(): string
    {
        return match ($this) {
            self::Pemerintah => 'Pemerintah',
            self::Swasta => 'Swasta',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pemerintah => 'bg-info text-white',
            self::Swasta => 'bg-warning text-dark',
        };
    }
}

