<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'session_type',
        'check_in_at',
        'check_out_at',
        'status',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForSession(Builder $query, string $sessionType): Builder
    {
        return $query->where('session_type', $sessionType);
    }

    public function scopeHadir(Builder $query): Builder
    {
        return $query->where('status', 'hadir');
    }
}

