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
        'invitation_sent_at',
        'invitation_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'invitation_sent_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Boot method to auto-assign certificate number, receipt number & QR code image file name.
     * Juga otomatis generate file fisik gambar QR Code setelah peserta berhasil dibuat/diperbarui.
     */
    protected static function booted(): void
    {
        static::creating(function (Participant $participant) {
            if (empty($participant->certificate_number)) {
                $participant->certificate_number = static::generateCertificateNumber();
            }

            if (empty($participant->receipt_number)) {
                $participant->receipt_number = $participant->certificate_number;
            }

            if (empty($participant->qr_code_path) && ! empty($participant->certificate_number)) {
                $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number);
                $participant->qr_code_path = "{$sanitized}.png";
            }
        });

        // Generate file QR code PNG setelah data berhasil disimpan
        static::created(function (Participant $participant) {
            static::generateQrCodeFile($participant);
        });

        static::updating(function (Participant $participant) {
            if ($participant->isDirty('certificate_number') && ! empty($participant->certificate_number)) {
                if (empty($participant->receipt_number) || $participant->receipt_number === $participant->getOriginal('certificate_number')) {
                    $participant->receipt_number = $participant->certificate_number;
                }
                $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number);
                $participant->qr_code_path = "{$sanitized}.png";
            }
        });

        // Re-generate QR code jika nomor sertifikat berubah
        static::updated(function (Participant $participant) {
            if ($participant->wasChanged('certificate_number')) {
                static::generateQrCodeFile($participant);
            }
        });
    }

    /**
     * Generate file PNG QR code ke public/uploads/qr_code/ dan simpan ke storage.
     * QR code mengarah ke halaman verifikasi sertifikat publik.
     */
    public static function generateQrCodeFile(Participant $participant): bool
    {
        if (empty($participant->certificate_number)) {
            return false;
        }

        try {
            $verifyUrl = route('certificate.verify', ['certificate_number' => $participant->certificate_number]);

            $options = new \chillerlan\QRCode\QROptions([
                'outputInterface'  => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                'eccLevel'         => \chillerlan\QRCode\Common\EccLevel::L,
                'outputBase64'     => false,
                'imageTransparent' => false,
                'scale'            => 10,
            ]);

            $imageData = (new \chillerlan\QRCode\QRCode($options))->render($verifyUrl);

            $dir = public_path('uploads/qr_code');
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number);
            file_put_contents("{$dir}/{$sanitized}.png", $imageData);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Helper untuk menghasilkan nomor sertifikat otomatis dengan kode acak (random) unik Linkpemda (LP).
     * Format: LP-[5-digit acak]-[Bulan Romawi]-[Tahun]
     * Contoh: LP-72941-XI-2026
     */
    public static function generateCertificateNumber(?CarbonInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'LP';
        $year = $date->format('Y');

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $monthRoman = $romanMonths[(int) $date->format('n')] ?? 'I';

        do {
            // 5 digit angka acak (10000 - 99999) agar nomor unik dan tidak dapat ditebak
            $randomCode = (string) random_int(10000, 99999);
            $number = "{$prefix}-{$randomCode}-{$monthRoman}-{$year}";
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Alias untuk backward compatibility
     */
    public static function generateNextCertificateNumber(?CarbonInterface $date = null): string
    {
        return static::generateCertificateNumber($date);
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
        if (empty($this->certificate_number)) {
            return null;
        }

        if (empty($this->qr_code_path)) {
            $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $this->certificate_number);
            $this->qr_code_path = "{$sanitized}.png";
        }

        if (str_starts_with($this->qr_code_path, 'http://') || str_starts_with($this->qr_code_path, 'https://')) {
            return $this->qr_code_path;
        }

        // Jika file belum ada di disk, generate otomatis
        if (! file_exists(public_path('uploads/qr_code/'.$this->qr_code_path)) &&
            ! file_exists(public_path('assets/images/qr_codes/'.$this->qr_code_path))) {
            static::generateQrCodeFile($this);
        }

        if (file_exists(public_path('uploads/qr_code/'.$this->qr_code_path))) {
            return asset('uploads/qr_code/'.$this->qr_code_path);
        }

        if (file_exists(public_path('assets/images/qr_codes/'.$this->qr_code_path))) {
            return asset('assets/images/qr_codes/'.$this->qr_code_path);
        }

        return asset('uploads/qr_code/'.$this->qr_code_path);
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
     * Cek apakah undangan & tiket barcode absensi sudah pernah dikirimkan ke email peserta.
     */
    public function getIsInvitationSentAttribute(): bool
    {
        return ! is_null($this->invitation_sent_at) && $this->invitation_status === 'sent';
    }

    /**
     * Label badge status email pengiriman undangan & tiket absensi.
     */
    public function getInvitationStatusBadgeAttribute(): array
    {
        if ($this->is_invitation_sent) {
            return [
                'label' => 'Terkirim',
                'class' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'mdi mdi-check-circle',
                'time' => $this->invitation_sent_at?->translatedFormat('d M Y, H:i').' WIB',
            ];
        }

        if ($this->invitation_status === 'failed') {
            return [
                'label' => 'Gagal Terkirim',
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'mdi mdi-close-circle',
                'time' => $this->invitation_sent_at?->translatedFormat('d M Y, H:i').' WIB',
            ];
        }

        return [
            'label' => 'Belum Dikirim',
            'class' => 'bg-secondary-subtle text-secondary border',
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

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceFor(string $sessionType): ?Attendance
    {
        if ($this->relationLoaded('attendances')) {
            return $this->attendances->firstWhere('session_type', $sessionType);
        }

        return $this->attendances()->where('session_type', $sessionType)->first();
    }

    public function isAttended(string $sessionType): bool
    {
        $att = $this->attendanceFor($sessionType);
        return $att !== null && $att->status === 'hadir';
    }
}
