<?php

namespace App\Enums;

enum PostStatus: string
{
  case Draft = 'draft';
  case Published = 'published';
  case Scheduled = 'scheduled';

  public function label(): string
  {
    return match ($this) {
      self::Draft => 'Draft',
      self::Published => 'Published',
      self::Scheduled => 'Scheduled',
    };
  }

  public function badgeClass(): string
  {
    return match ($this) {
      self::Draft => 'badge-soft-secondary',
      self::Published => 'badge-soft-success',
      self::Scheduled => 'badge-soft-warning',
    };
  }
}
