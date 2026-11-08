<?php

namespace App\Livewire\Admin\Invoice;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Invoice Linkpemda')]
class Index extends Component
{
    use WithPagination;

    public string $pageTitle = 'Invoice Linkpemda';

    public string $search = '';

    public string $selectedStatus = '';

    public string $selectedPaymentStatus = '';

    public string $selectedMenu = '';

    public int $perPage = 10;

    // Delete modal state
    public ?string $invoiceToDeleteId = null;

    public string $invoiceToDeleteNumber = '';

    // Reject modal state
    public ?string $invoiceToRejectId = null;

    public string $invoiceToRejectNumber = '';

    public string $rejectionNote = '';

    protected string $paginationTheme = 'bootstrap';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedPaymentStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedMenu(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedStatus', 'selectedPaymentStatus', 'selectedMenu']);
        $this->resetPage();
    }

    /**
     * Kirim invoice resmi dan lampiran PDF via email ke instansi klien
     */
    public function sendInvoiceEmail(string $id): void
    {
        $invoice = Invoice::findOrFail($id);

        if (empty($invoice->email)) {
            session()->flash('error', "Invoice \"{$invoice->invoice_number}\" belum memiliki alamat email. Silakan edit invoice untuk melengkapi email.");

            return;
        }

        try {
            Mail::to($invoice->email)->send(new InvoiceMail($invoice));

            $invoice->update([
                'email_sent_at' => Carbon::now(),
            ]);

            session()->flash('success', "Invoice \"{$invoice->invoice_number}\" berhasil dikirimkan ke email {$invoice->email}!");
        } catch (\Throwable $e) {
            session()->flash('error', "Gagal mengirimkan email ke {$invoice->email}: " . $e->getMessage());
        }
    }

    /**
     * Admin ACC / Approve Invoice
     */
    public function approveInvoice(string $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->approve(auth()->user());

        session()->flash('success', "Invoice \"{$invoice->invoice_number}\" berhasil disetujui (ACC).");
    }

    /**
     * Admin menandai invoice sebagai Lunas
     */
    public function markAsPaid(string $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->markAsPaid();

        session()->flash('success', "Invoice \"{$invoice->invoice_number}\" berhasil ditandai Lunas.");
    }

    /**
     * Buka modal Tolak Invoice
     */
    public function confirmReject(string $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceToRejectId = $invoice->id;
        $this->invoiceToRejectNumber = $invoice->invoice_number;
        $this->rejectionNote = '';

        $this->dispatch('open-reject-modal');
    }

    /**
     * Eksekusi Tolak Invoice
     */
    public function rejectInvoice(): void
    {
        if (! $this->invoiceToRejectId) {
            return;
        }

        $invoice = Invoice::findOrFail($this->invoiceToRejectId);
        $invoice->reject($this->rejectionNote ?: 'Ditolak oleh admin.');

        session()->flash('info', "Invoice \"{$invoice->invoice_number}\" ditolak.");
        $this->dispatch('close-reject-modal');
        $this->reset(['invoiceToRejectId', 'invoiceToRejectNumber', 'rejectionNote']);
    }

    /**
     * Buka modal Hapus Invoice
     */
    public function confirmDelete(string $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceToDeleteId = $invoice->id;
        $this->invoiceToDeleteNumber = $invoice->invoice_number;

        $this->dispatch('open-delete-modal');
    }

    /**
     * Eksekusi Hapus Invoice
     */
    public function delete(): void
    {
        if (! $this->invoiceToDeleteId) {
            return;
        }

        $invoice = Invoice::findOrFail($this->invoiceToDeleteId);
        $number = $invoice->invoice_number;
        $invoice->delete();

        session()->flash('success', "Invoice \"{$number}\" berhasil dihapus.");
        $this->dispatch('close-delete-modal');
        $this->reset(['invoiceToDeleteId', 'invoiceToDeleteNumber']);
    }

    public function render()
    {
        $totalInvoices = Invoice::count();
        $approvedCount = Invoice::where('status', 'approved')->count();
        $pendingCount = Invoice::where('status', 'pending')->count();
        $totalAmount = (float) Invoice::sum('total_amount');

        $invoices = Invoice::query()
            ->with('approver')
            ->when(filled($this->search), function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('activity_theme', 'like', $term)
                        ->orWhere('event_location', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->when(filled($this->selectedStatus), function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->when(filled($this->selectedPaymentStatus), function ($query) {
                $query->where('payment_status', $this->selectedPaymentStatus);
            })
            ->when(filled($this->selectedMenu), function ($query) {
                $query->where('menu_type', $this->selectedMenu);
            })
            ->latest('invoice_date')
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.invoice.index', [
            'invoices' => $invoices,
            'totalInvoices' => $totalInvoices,
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'totalAmount' => $totalAmount,
        ]);
    }
}
