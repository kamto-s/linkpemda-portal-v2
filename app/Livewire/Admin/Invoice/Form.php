<?php

namespace App\Livewire\Admin\Invoice;

use App\Enums\InvoicePaymentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Form Invoice Linkpemda')]
class Form extends Component
{
    public ?Invoice $invoice = null;

    public bool $isEditing = false;

    // Header & Identitas Invoice (Otomatis oleh sistem)
    public string $invoice_number = '';

    public string $invoice_date = '';

    // Data Pelanggan / Instansi
    public string $company_name = '';

    public string $email = '';

    public string $address = '';

    public string $phone = '';

    // Data Kegiatan
    public string $activity_theme = '';

    public string $menu_type = 'Meeting';

    public string $payment_method = 'Transfer / Cash';

    public string $event_start_date = '';

    public ?string $event_end_date = null;

    public string $event_time = '08:00';

    public string $event_location = '';

    // Data Peserta (Repeater)
    public array $participants = [''];

    public int $participant_count = 1;

    // Rincian Biaya
    public string $price_per_participant = '0';

    public string $deposit_amount = '0';

    public float $total_amount = 0;

    public float $remaining_amount = 0;

    // 1. Status Persetujuan (ACC Admin)
    public string $status = 'pending';

    // 2. Status Pembayaran Terpisah
    public string $payment_status = 'unpaid';

    public string $notes = '';

    public function mount(Invoice|string|null $invoice = null): void
    {
        if ($invoice) {
            $this->invoice = $invoice instanceof Invoice ? $invoice : Invoice::findOrFail($invoice);
            $this->isEditing = true;

            $this->invoice_number = $this->invoice->invoice_number;
            $this->invoice_date = $this->invoice->invoice_date ? $this->invoice->invoice_date->format('Y-m-d') : '';
            $this->company_name = $this->invoice->company_name ?? '';
            $this->email = $this->invoice->email ?? '';
            $this->address = $this->invoice->address ?? '';
            $this->phone = $this->invoice->phone ?? '';
            $this->activity_theme = $this->invoice->activity_theme ?? '';
            $this->menu_type = $this->invoice->menu_type ?? 'Meeting';
            $this->payment_method = $this->invoice->payment_method ?? 'Transfer / Cash';

            $this->event_start_date = $this->invoice->event_start_date ? $this->invoice->event_start_date->format('Y-m-d') : '';
            $this->event_end_date = $this->invoice->event_end_date ? $this->invoice->event_end_date->format('Y-m-d') : null;

            // Ekstrak jam dari data tersimpan (misal: "08.00 WIB" -> time "08:00")
            $rawTime = (string) ($this->invoice->event_time ?? '08.00 WIB');
            if (preg_match('/(\d{1,2})[:.](\d{2})/', $rawTime, $timeMatch)) {
                $this->event_time = str_pad($timeMatch[1], 2, '0', STR_PAD_LEFT) . ':' . $timeMatch[2];
            } else {
                $this->event_time = '08:00';
            }

            $this->event_location = $this->invoice->event_location ?? '';

            $rawParticipants = (array) ($this->invoice->participants ?? []);
            $this->participants = ! empty($rawParticipants) ? array_values($rawParticipants) : [''];

            $this->price_per_participant = number_format((float) $this->invoice->price_per_participant, 0, ',', '.');
            $this->deposit_amount = number_format((float) $this->invoice->deposit_amount, 0, ',', '.');
            $this->notes = $this->invoice->notes ?? '';

            $this->status = $this->invoice->status instanceof InvoiceStatus ? $this->invoice->status->value : ($this->invoice->status ?? 'pending');
            $this->payment_status = $this->invoice->payment_status instanceof InvoicePaymentStatus ? $this->invoice->payment_status->value : ($this->invoice->payment_status ?? 'unpaid');
        } else {
            $this->invoice_date = Carbon::now()->format('Y-m-d');
            $this->invoice_number = '';
            $this->participants = [''];
            $this->menu_type = 'Meeting';
            $this->payment_method = 'Transfer / Cash';
            $this->event_time = '08:00';
            $this->price_per_participant = '0';
            $this->deposit_amount = '0';
            $this->status = 'pending';
            $this->payment_status = 'unpaid';
        }

        $this->recalculateTotals();
    }

    public function updatedInvoiceDate(): void
    {
        if (! $this->isEditing) {
            $date = ! empty($this->invoice_date) ? Carbon::parse($this->invoice_date) : Carbon::now();
            $this->invoice_number = Invoice::generateInvoiceNumber($date);
        }
    }

    public function regenerateInvoiceNumber(): void
    {
        $date = ! empty($this->invoice_date) ? Carbon::parse($this->invoice_date) : Carbon::now();
        $this->invoice_number = Invoice::generateInvoiceNumber($date);
    }

    public function addParticipant(): void
    {
        $this->participants[] = '';
        $this->recalculateTotals();
    }

    public function removeParticipant(int $index): void
    {
        if (isset($this->participants[$index])) {
            unset($this->participants[$index]);
            $this->participants = array_values($this->participants);
        }

        if (empty($this->participants)) {
            $this->participants = [''];
        }

        $this->recalculateTotals();
    }

    public function updatedParticipants(): void
    {
        $this->recalculateTotals();
    }

    public function updatedPricePerParticipant(): void
    {
        $this->recalculateTotals();
    }

    public function updatedDepositAmount(): void
    {
        $this->recalculateTotals();
    }

    public function recalculateTotals(): void
    {
        $validParticipants = array_values(array_filter($this->participants, fn ($p) => trim((string) $p) !== ''));
        $this->participant_count = count($validParticipants) > 0 ? count($validParticipants) : max(1, count($this->participants));

        $cleanPrice = (float) preg_replace('/[^\d]/', '', (string) $this->price_per_participant);
        $cleanDeposit = (float) preg_replace('/[^\d]/', '', (string) $this->deposit_amount);

        $this->total_amount = $this->participant_count * $cleanPrice;
        $this->remaining_amount = max(0, $this->total_amount - $cleanDeposit);

        // Sinkronisasi status pembayaran otomatis berdasarkan nominal
        if ($this->remaining_amount <= 0 && $this->total_amount > 0) {
            $this->payment_status = 'paid';
        } elseif ($cleanDeposit > 0 && $this->remaining_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
    }

    public function getFormattedEventDatePreviewProperty(): string
    {
        if (empty($this->event_start_date)) {
            return '-';
        }

        $start = Carbon::parse($this->event_start_date)->locale('id');

        if (empty($this->event_end_date) || $this->event_start_date === $this->event_end_date) {
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

    public function getTerbilangTotalProperty(): string
    {
        if (! function_exists('terbilang') || $this->total_amount <= 0) {
            return 'Nol Rupiah';
        }

        return ucwords(terbilang($this->total_amount)) . ' Rupiah';
    }

    public function getTerbilangRemainingProperty(): string
    {
        if (! function_exists('terbilang') || $this->remaining_amount <= 0) {
            return 'Nol Rupiah';
        }

        return ucwords(terbilang($this->remaining_amount)) . ' Rupiah';
    }

    public function save()
    {
        $this->validate([
            'invoice_date' => 'required|date',
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:100',
            'activity_theme' => 'required|string',
            'menu_type' => 'required|string|max:50',
            'payment_method' => 'required|string|max:100',
            'event_start_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_time' => 'required',
            'event_location' => 'required|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'nullable|string|max:255',
            'status' => 'required|string|in:pending,approved,rejected',
            'payment_status' => 'required|string|in:unpaid,partial,paid',
        ], [
            'invoice_date.required' => 'Tanggal invoice wajib diisi.',
            'company_name.required' => 'Nama instansi/perusahaan wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'activity_theme.required' => 'Tema kegiatan wajib diisi.',
            'event_start_date.required' => 'Tanggal mulai kegiatan wajib diisi.',
            'event_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'event_location.required' => 'Lokasi kegiatan wajib diisi.',
        ]);

        $this->recalculateTotals();

        // Bersihkan list peserta dari string kosong
        $cleanParticipants = array_values(array_filter(
            array_map('trim', $this->participants),
            fn ($p) => $p !== ''
        ));

        if (empty($cleanParticipants)) {
            $cleanParticipants = ['Peserta Bimtek'];
        }

        $cleanPrice = (float) preg_replace('/[^\d]/', '', (string) $this->price_per_participant);
        $cleanDeposit = (float) preg_replace('/[^\d]/', '', (string) $this->deposit_amount);

        // Nomor invoice: jika buat baru otomatis di-generate unik oleh sistem
        if ($this->isEditing && $this->invoice) {
            $invoiceNumber = $this->invoice->invoice_number;
        } else {
            $invoiceNumber = Invoice::generateInvoiceNumber(Carbon::parse($this->invoice_date));
        }

        $payload = [
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $this->invoice_date,
            'company_name' => trim($this->company_name),
            'email' => trim($this->email) ?: null,
            'address' => trim($this->address) ?: null,
            'phone' => trim($this->phone) ?: null,
            'activity_theme' => trim($this->activity_theme),
            'menu_type' => $this->menu_type,
            'payment_method' => $this->payment_method,
            'event_start_date' => $this->event_start_date,
            'event_end_date' => $this->event_end_date ?: null,
            'event_time' => !empty($this->event_time) ? (str_replace(':', '.', trim($this->event_time)) . ' WIB') : '08.00 WIB',
            'event_location' => trim($this->event_location),
            'participants' => $cleanParticipants,
            'participant_count' => count($cleanParticipants),
            'price_per_participant' => $cleanPrice,
            'total_amount' => $this->total_amount,
            'deposit_amount' => $cleanDeposit,
            'remaining_amount' => $this->remaining_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'notes' => trim($this->notes) ?: null,
        ];

        // Jika status diset ke approved oleh admin saat save
        if ($this->status === 'approved' && (! $this->invoice || ! $this->invoice->approved_by)) {
            $payload['approved_by'] = auth()->id();
            $payload['approved_at'] = Carbon::now();
        }

        if ($this->isEditing && $this->invoice) {
            $this->invoice->update($payload);
            session()->flash('success', "Invoice \"{$this->invoice->invoice_number}\" berhasil diperbarui.");
        } else {
            $newInvoice = Invoice::create($payload);
            session()->flash('success', "Invoice \"{$newInvoice->invoice_number}\" berhasil dibuat.");
        }

        return $this->redirect(route('admin.invoice.linkpemda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.invoice.form', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
