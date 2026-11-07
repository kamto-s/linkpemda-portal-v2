<?php

namespace App\Models;

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends BaseModel
{
    use HasFactory;

    protected $table = 'invoices';

    protected $casts = [
        'invoice_date' => 'date',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'approved_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'participants' => 'array',
        'participant_count' => 'integer',
        'price_per_participant' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'status' => InvoiceStatus::class,
        'payment_status' => InvoicePaymentStatus::class,
    ];

    /**
     * Romawi untuk bulan kalender.
     */
    public const ROMAN_MONTHS = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Admin yang menyetujui (ACC) invoice ini.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate nomor invoice otomatis dengan format: LP-00001-XI-2026
     */
    public static function generateInvoiceNumber(?\DateTimeInterface $date = null, string $prefix = 'LP'): string
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        $year = $date->year;
        $month = (int) $date->month;
        $romanMonth = self::ROMAN_MONTHS[$month] ?? 'I';

        // Cari nomor invoice terakhir untuk prefix dan tahun yang sama
        $lastInvoice = static::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}-%-{$year}")
            ->orderBy('created_at', 'desc')
            ->first();

        $nextSequence = 1;
        if ($lastInvoice && preg_match("/^{$prefix}-(\d+)-[A-Z]+-{$year}$/", $lastInvoice->invoice_number, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        $sequenceStr = str_pad((string) $nextSequence, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$sequenceStr}-{$romanMonth}-{$year}";
    }

    /**
     * Format rentang tanggal pelaksanaan kegiatan yang cerdas:
     * - Jika bulan & tahun sama: 18 - 21 November 2026
     * - Jika beda bulan (tahun sama): 28 Oktober - 2 November 2026
     * - Jika beda tahun: 28 Desember 2026 - 2 Januari 2027
     * - Jika 1 hari saja: 18 November 2026
     */
    public function getFormattedEventDateAttribute(): string
    {
        if (! $this->event_start_date) {
            return '-';
        }

        $start = Carbon::parse($this->event_start_date)->locale('id');

        if (! $this->event_end_date || $this->event_start_date->isSameDay($this->event_end_date)) {
            return $start->translatedFormat('d F Y');
        }

        $end = Carbon::parse($this->event_end_date)->locale('id');

        if ($start->year === $end->year) {
            if ($start->month === $end->month) {
                return $start->format('j') . ' - ' . $end->format('j') . ' ' . $end->translatedFormat('F Y');
            }

            return $start->translatedFormat('j F') . ' - ' . $end->translatedFormat('j F Y');
        }

        return $start->translatedFormat('j F Y') . ' - ' . $end->translatedFormat('j F Y');
    }

    /**
     * Format tanggal invoice: 2 November 2026
     */
    public function getFormattedInvoiceDateAttribute(): string
    {
        if (! $this->invoice_date) {
            return '-';
        }

        return Carbon::parse($this->invoice_date)->locale('id')->translatedFormat('d F Y');
    }

    /**
     * Terbilang untuk Total Harga
     */
    public function getTerbilangTotalAttribute(): string
    {
        if (! function_exists('terbilang')) {
            return '';
        }

        $nominal = (float) $this->total_amount;
        if ($nominal <= 0) {
            return 'Nol Rupiah';
        }

        return ucwords(terbilang($nominal)) . ' Rupiah';
    }

    /**
     * Terbilang untuk Sisa Pembayaran
     */
    public function getTerbilangRemainingAttribute(): string
    {
        if (! function_exists('terbilang')) {
            return '';
        }

        $nominal = (float) $this->remaining_amount;
        if ($nominal <= 0) {
            return 'Nol Rupiah';
        }

        return ucwords(terbilang($nominal)) . ' Rupiah';
    }

    /**
     * Setujui (ACC) Invoice oleh Admin
     */
    public function approve(string|User $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $this->update([
            'status' => InvoiceStatus::Approved,
            'approved_by' => $userId,
            'approved_at' => Carbon::now(),
            'rejection_note' => null,
        ]);
    }

    /**
     * Tolak Invoice
     */
    public function reject(?string $note = null): void
    {
        $this->update([
            'status' => InvoiceStatus::Rejected,
            'rejection_note' => $note,
        ]);
    }

    /**
     * Tandai Invoice Lunas
     */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => InvoicePaymentStatus::Paid,
            'remaining_amount' => 0,
        ]);
    }
}
