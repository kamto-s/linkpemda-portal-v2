<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrenParticipant extends BaseModel
{
    use HasFactory;

    protected $table = 'tren_participants';

    protected $fillable = [
        'certificate_number',
        'qr_code_path',
        'name',
        'email',
        'phone',
        'position',
        'institution_name',
        'theme',
        'certificate_date',
        'event_venue',
        'event_location',
        'registration_date',
        'receipt_number',
        'receipt_date',
        'amount',
        'email_sent_at',
        'email_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Boot method to auto-assign certificate number & receipt number.
     * Catatan: QR code & Email saat ini belum diaktifkan sesuai permintaan (ember siap).
     */
    protected static function booted(): void
    {
        static::creating(function (TrenParticipant $participant) {
            if (empty($participant->certificate_number)) {
                $participant->certificate_number = static::generateCertificateNumber();
            }

            if (empty($participant->receipt_number)) {
                $participant->receipt_number = $participant->certificate_number;
            }
        });

        static::updating(function (TrenParticipant $participant) {
            if ($participant->isDirty('certificate_number') && !empty($participant->certificate_number)) {
                if (empty($participant->receipt_number) || $participant->receipt_number === $participant->getOriginal('certificate_number')) {
                    $participant->receipt_number = $participant->certificate_number;
                }
            }
        });
    }

    /**
     * Helper menghasilkan nomor sertifikat otomatis untuk Tren Media Diklat.
     * Format: [acak 5 digit]/TREN_SERT/[Bulan Romawi]/[Tahun]
     * Contoh: 38192/TREN_SERT/XI/2026 atau 625/TREN_SERT/VI/2026
     */
    public static function generateCertificateNumber(?CarbonInterface $date = null): string
    {
        $date = $date ?? now();
        $year = $date->format('Y');

        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];
        $monthRoman = $romanMonths[(int) $date->format('n')] ?? 'I';

        do {
            $randomCode = (string) random_int(10000, 99999);
            $number = "{$randomCode}/TREN_SERT/{$monthRoman}/{$year}";
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Mutator agar amount aman menerima angka murni maupun string 'Rp 5.000.000' atau kosong.
     */
    public function setAmountAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['amount'] = 0;

            return;
        }

        $this->attributes['amount'] = is_numeric($value)
            ? (float) $value
            : (float) preg_replace('/[^0-9]/', '', (string) $value);
    }

    /**
     * Format nominal biaya dalam format Rupiah (Rp).
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * Cek apakah Sertifikat sudah pernah dikirimkan ke email peserta.
     */
    public function getIsEmailSentAttribute(): bool
    {
        return !is_null($this->email_sent_at) && $this->email_status === 'sent';
    }

    /**
     * Label badge status email pengiriman sertifikat.
     */
    public function getEmailStatusBadgeAttribute(): array
    {
        if ($this->is_email_sent) {
            return [
                'label' => 'Terkirim',
                'class' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'mdi mdi-check-circle',
                'time' => $this->email_sent_at?->translatedFormat('d M Y, H:i') . ' WIB',
            ];
        }

        if ($this->email_status === 'failed') {
            return [
                'label' => 'Gagal Terkirim',
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'mdi mdi-close-circle',
                'time' => $this->email_sent_at?->translatedFormat('d M Y, H:i') . ' WIB',
            ];
        }

        return [
            'label' => 'Belum Dikirim',
            'class' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'icon' => 'mdi mdi-clock-outline',
            'time' => null,
        ];
    }

    /**
     * Scope pencarian peserta Tren Media Diklat.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('certificate_number', 'like', "%{$term}%")
                ->orWhere('receipt_number', 'like', "%{$term}%")
                ->orWhere('institution_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('theme', 'like', "%{$term}%");
        });
    }

    // =========================================================================
    // Backward Compatibility Aliases
    // =========================================================================

    protected function noCertf(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->certificate_number,
            set: fn($value) => ['certificate_number' => $value],
        );
    }

    protected function jabatan(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->position,
            set: fn($value) => ['position' => $value],
        );
    }

    protected function instansi(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->institution_name,
            set: fn($value) => ['institution_name' => $value],
        );
    }

    protected function tema(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->theme,
            set: fn($value) => ['theme' => $value],
        );
    }

    protected function biaya(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->amount,
            set: fn($value) => ['amount' => $value],
        );
    }
}

