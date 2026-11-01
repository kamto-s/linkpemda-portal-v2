<?php

namespace App\Livewire\Admin\Participant;

use App\Mail\CertificateMail;
use App\Models\Participant;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Data Peserta Linkpemda')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Data Peserta Linkpemda';

    public string $search = '';

    public string $selectedEmailStatus = '';

    public string $selectedYear = '';

    public int $perPage = 10;

    // Checkbox seleksi masal
    public array $selectedParticipants = [];

    public bool $selectAll = false;

    // Delete confirmation modal state
    public ?string $participantToDeleteId = null;

    public string $participantToDeleteName = '';

    // Modal QR Code Preview
    public ?string $previewQrUrl = null;

    public string $previewQrTitle = '';

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat data peserta.'
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedEmailStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedEmailStatus', 'selectedYear', 'selectedParticipants', 'selectAll']);
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedParticipants = $this->getCurrentPageParticipantIds();
        } else {
            $this->selectedParticipants = [];
        }
    }

    protected function getCurrentPageParticipantIds(): array
    {
        return $this->getQuery()
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    public function confirmDelete(string $id): void
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        $participant = Participant::findOrFail($id);
        $this->participantToDeleteId = $participant->id;
        $this->participantToDeleteName = $participant->name;

        $this->dispatch('open-delete-modal');
    }

    public function delete(?string $id = null): void
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        $targetId = $id ?: $this->participantToDeleteId;
        if (! $targetId) {
            return;
        }

        $participant = Participant::findOrFail($targetId);
        $name = $participant->name;
        $participant->delete();

        session()->flash('success', "Data peserta \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['participantToDeleteId', 'participantToDeleteName']);
    }

    /**
     * Kirim e-sertifikat via email ke peserta perorangan.
     */
    public function sendCertificateEmail(string $id): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim e-sertifikat.'
        );

        $participant = Participant::findOrFail($id);

        if (empty($participant->email)) {
            session()->flash('error', "Gagal: Peserta \"{$participant->name}\" belum memiliki alamat email. Silakan edit data dan lengkapi email terlebih dahulu.");

            return;
        }

        try {
            Mail::to($participant->email)->send(new CertificateMail($participant));

            $participant->update([
                'email_sent_at' => now(),
                'email_status' => 'sent',
            ]);

            session()->flash('success', "E-Sertifikat berhasil dikirimkan ke email {$participant->email} ({$participant->name})!");
        } catch (\Throwable $e) {
            $participant->update(['email_status' => 'failed']);
            session()->flash('error', "Gagal mengirimkan email ke {$participant->email}: ".$e->getMessage());
        }
    }

    /**
     * Kirim e-sertifikat masal ke seluruh peserta yang dicentang.
     */
    public function sendBulkCertificateEmail(): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim e-sertifikat.'
        );

        if (empty($this->selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta untuk pengiriman e-sertifikat masal.');

            return;
        }

        $participants = Participant::whereIn('id', $this->selectedParticipants)->get();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($participants as $participant) {
            if (empty($participant->email)) {
                $failedCount++;

                continue;
            }

            try {
                Mail::to($participant->email)->send(new CertificateMail($participant));
                $participant->update([
                    'email_sent_at' => now(),
                    'email_status' => 'sent',
                ]);
                $sentCount++;
            } catch (\Throwable $e) {
                $participant->update(['email_status' => 'failed']);
                $failedCount++;
            }
        }

        $this->selectedParticipants = [];
        $this->selectAll = false;

        session()->flash('success', "Pengiriman masal selesai: {$sentCount} e-sertifikat berhasil dikirim.".($failedCount > 0 ? " ({$failedCount} dilewati/gagal karena tanpa email)" : ''));
    }

    public function showQrModal(string $id): void
    {
        $participant = Participant::findOrFail($id);
        $this->previewQrUrl = $participant->qr_code_url;
        $this->previewQrTitle = "QR Code Sertifikat: {$participant->name} ({$participant->certificate_number})";

        $this->dispatch('open-qr-modal');
    }

    protected function getQuery()
    {
        return Participant::query()
            ->search($this->search)
            ->when($this->selectedEmailStatus !== '', function ($q) {
                $q->where('email_status', $this->selectedEmailStatus);
            })
            ->when($this->selectedYear !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('certificate_number', 'like', "%-{$this->selectedYear}")
                        ->orWhere('certificate_date', 'like', "%{$this->selectedYear}%");
                });
            })
            ->latest('created_at');
    }

    public function render()
    {
        $participants = $this->getQuery()->paginate($this->perPage);

        // Daftar tahun untuk filter
        $currentYear = (int) now()->format('Y');
        $years = range($currentYear, $currentYear - 4);

        // Statistik ringkas & total nominal kwitansi
        $totalParticipants = Participant::count();
        $emailSentCount = Participant::where('email_status', 'sent')->count();
        $emailPendingCount = Participant::where('email_status', 'pending')->count();
        $totalAmount = Participant::sum('amount');

        return view('livewire.admin.participant.index', [
            'participants' => $participants,
            'years' => $years,
            'totalParticipants' => $totalParticipants,
            'emailSentCount' => $emailSentCount,
            'emailPendingCount' => $emailPendingCount,
            'totalAmount' => $totalAmount,
        ]);
    }
}

