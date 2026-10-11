<?php

namespace App\Enums;

enum PostType: string
{
  case Artikel = 'artikel';
  case Materi = 'materi';

  public function label(): string
  {
    return match ($this) {
      self::Artikel => 'Artikel',
      self::Materi => 'Materi',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Artikel => 'badge-soft-info',
      self::Materi => 'badge-soft-primary',
    };
  }
}
