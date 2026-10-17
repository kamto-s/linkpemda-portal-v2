<?php

namespace App\Enums;

enum EmailTemplateCategory: string
{
    case Marketing = 'marketing';
    case Info = 'info';
    case Agenda = 'agenda';
    case Reminder = 'reminder';
    case Formal = 'formal';

    public function label(): string
    {
        return match ($this) {
            self::Marketing => 'Penawaran Pelatihan',
            self::Info => 'Info & Tips Santai',
            self::Agenda => 'Agenda & Webinar',
            self::Reminder => 'Reminder Kuota',
            self::Formal => 'Format Kedinasan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Marketing => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::Info => 'bg-warning-subtle text-warning border border-warning-subtle',
            self::Agenda => 'bg-info-subtle text-info border border-info-subtle',
            self::Reminder => 'bg-orange-subtle text-orange border border-orange-subtle',
            self::Formal => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Marketing => 'mdi mdi-school-outline',
            self::Info => 'mdi mdi-lightbulb-on-outline',
            self::Agenda => 'mdi mdi-calendar-month-outline',
            self::Reminder => 'mdi mdi-clock-alert-outline',
            self::Formal => 'mdi mdi-file-document-outline',
        };
    }
}

