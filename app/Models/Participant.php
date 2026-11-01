<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends BaseModel
{
    use HasFactory;

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
     * Boot method to auto-assign QR code image file name based on certificate_number if empty.
     */
    protected static function booted(): void
    {
        static::creating(function (Participant $participant) {
            if (empty($participant->qr_code_path) && ! empty($participant->certificate_number)) {
                $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number);
                $participant->qr_code_path = $sanitized.'.png';
            }
        });
    }

    /**
     * Helper untuk menghasilkan nomor sertifikat otomatis berikutnya sesuai format Linkpemda (LP).
     * Contoh: LP-00171-II-2025
     */
    public static function generateNextCertificateNumber(?CarbonInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'LP';
        $year = $date->format('Y');

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $monthRoman = $romanMonths[(int) $date->format('n')] ?? 'I';

        $latest = static::where('certificate_number', 'like', "{$prefix}-%-{$year}")
            ->orderByRaw('LENGTH(certificate_number) DESC, certificate_number DESC')
            ->value('certificate_number');

        $nextNumber = 1;
        if ($latest && preg_match('/'.$prefix.'-(\d+)-/i', $latest, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        $paddedNumber = str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$paddedNumber}-{$monthRoman}-{$year}";
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
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * Mendapatkan URL gambar QR code untuk pratinjau sertifikat.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if (empty($this->qr_code_path)) {
            return null;
        }

        if (str_starts_with($this->qr_code_path, 'http://') || str_starts_with($this->qr_code_path, 'https://')) {
            return $this->qr_code_path;
        }

        if (file_exists(public_path('assets/images/qr_codes/'.$this->qr_code_path))) {
            return asset('assets/images/qr_codes/'.$this->qr_code_path);
        }

        if (file_exists(public_path('uploads/qr_code/'.$this->qr_code_path))) {
            return asset('uploads/qr_code/'.$this->qr_code_path);
        }

        return asset('storage/qr_codes/'.$this->qr_code_path);
    }

    /**
     * Cek apakah e-sertifikat sudah pernah dikirimkan ke email peserta.
     */
    public function getIsEmailSentAttribute(): bool
    {
        return ! is_null($this->email_sent_at) && $this->email_status === 'sent';
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
                'time' => $this->email_sent_at?->translatedFormat('d M Y, H:i').' WIB',
            ];
        }

        if ($this->email_status === 'failed') {
            return [
                'label' => 'Gagal Terkirim',
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'mdi mdi-close-circle',
                'time' => $this->email_sent_at?->translatedFormat('d M Y, H:i').' WIB',
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
     * Scope pencarian peserta.
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
    // Backward Compatibility Aliases (Opsional jika memanggil nama Indonesia)
    // =========================================================================

    protected function noCertf(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->certificate_number,
            set: fn ($value) => ['certificate_number' => $value],
        );
    }

    protected function jabatan(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->position,
            set: fn ($value) => ['position' => $value],
        );
    }

    protected function instansi(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->institution_name,
            set: fn ($value) => ['institution_name' => $value],
        );
    }

    protected function tema(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->theme,
            set: fn ($value) => ['theme' => $value],
        );
    }

    protected function biaya(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount,
            set: fn ($value) => ['amount' => $value],
        );
    }
}
