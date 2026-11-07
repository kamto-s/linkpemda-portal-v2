<?php

namespace App\Livewire\Admin\Invoice;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Detail Invoice Linkpemda')]
class Show extends Component
{
    public Invoice $invoice;

    public string $rejectionNote = '';

    public function mount(Invoice|string $invoice): void
    {
        $this->invoice = $invoice instanceof Invoice ? $invoice : Invoice::findOrFail($invoice);
        $this->invoice->load('approver');
    }

    /**
     * Kirim berkas PDF Invoice ke alamat email instansi klien
     */
    public function sendInvoiceEmail(): void
    {
        if (empty($this->invoice->email)) {
            session()->flash('error', "Invoice {$this->invoice->invoice_number} belum memiliki alamat email terdaftar.");

            return;
        }

        try {
            Mail::to($this->invoice->email)->send(new InvoiceMail($this->invoice));

            $this->invoice->update([
                'email_sent_at' => Carbon::now(),
            ]);

            $this->invoice->refresh();
            session()->flash('success', "Invoice berhasil dikirimkan ke email {$this->invoice->email}!");
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal mengirimkan email ke {$this->invoice->email}: " . $e->getMessage());
        }
    }

    /**
     * Admin ACC / Approve Invoice
     */
    public function approveInvoice(): void
    {
        $this->invoice->approve(auth()->user());
        $this->invoice->refresh();
        $this->invoice->load('approver');

        session()->flash('success', "Invoice \"{$this->invoice->invoice_number}\" berhasil disetujui (ACC).");
    }

    /**
     * Admin menandai invoice sebagai Lunas
     */
    public function markAsPaid(): void
    {
        $this->invoice->markAsPaid();
        $this->invoice->refresh();

        session()->flash('success', "Invoice \"{$this->invoice->invoice_number}\" berhasil ditandai Lunas.");
    }

    /**
     * Eksekusi Tolak Invoice
     */
    public function rejectInvoice(): void
    {
        $this->invoice->reject($this->rejectionNote ?: 'Ditolak oleh admin.');
        $this->invoice->refresh();

        session()->flash('info', "Invoice \"{$this->invoice->invoice_number}\" ditolak.");
        $this->dispatch('close-reject-modal');
        $this->reset('rejectionNote');
    }

    /**
     * Hapus Invoice
     */
    public function delete()
    {
        $number = $this->invoice->invoice_number;
        $this->invoice->delete();

        session()->flash('success', "Invoice \"{$number}\" berhasil dihapus.");

        return $this->redirect(route('admin.invoice.linkpemda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.invoice.show');
    }
}

