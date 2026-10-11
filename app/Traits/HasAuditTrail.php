<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait HasAuditTrail
{
  use SoftDeletes;

  public static function bootHasAuditTrail(): void
  {
    static::creating(function ($model) {
      if (Auth::check() && empty($model->created_by)) {
        $model->created_by = Auth::id();
      }
    });

    static::updating(function ($model) {
      if (Auth::check()) {
        $model->updated_by = Auth::id();
      }
    });

    static::deleting(function ($model) {
      if (Auth::check() && in_array(SoftDeletes::class, class_uses_recursive($model))) {
        $model->deleted_by = Auth::id();
        $model->saveQuietly();
      }
    });
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function deleter(): BelongsTo
  {
    return $this->belongsTo(User::class, 'deleted_by');
  }
}
