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

    public string $selectedMonth = '';

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

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedEmailStatus', 'selectedMonth', 'selectedYear', 'selectedParticipants', 'selectAll']);
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
     * Kirim email undangan & tiket barcode absensi ke peserta perorangan.
     */
    public function sendInvitationEmail(string $id): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim undangan.'
        );

        $participant = Participant::findOrFail($id);

        if (empty($participant->email)) {
            session()->flash('error', "Gagal: Peserta \"{$participant->name}\" belum memiliki alamat email. Silakan lengkapi email terlebih dahulu.");

            return;
        }

        if (empty($participant->qr_code_path) || !file_exists(public_path('uploads/qr_code/' . $participant->qr_code_path))) {
            Participant::generateQrCodeFile($participant);
        }

        try {
            Mail::to($participant->email)->send(new \App\Mail\InvitationTicketMail($participant));

            $update = [];
            if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_sent_at')) {
                $update['invitation_sent_at'] = now();
                $update['invitation_status'] = 'sent';
            }
            if (!empty($update)) {
                $participant->update($update);
            }

            session()->flash('success', "Undangan & tiket barcode absensi berhasil dikirim ke {$participant->email} ({$participant->name})!");
        } catch (\Throwable $e) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_status')) {
                $participant->update(['invitation_status' => 'failed']);
            }
            session()->flash('error', "Gagal mengirimkan undangan ke {$participant->email}: ".$e->getMessage());
        }
    }

    /**
     * Kirim email undangan & tiket barcode absensi masal ke peserta terpilih.
     */
    public function sendBulkInvitationEmail(): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim undangan.'
        );

        if (empty($this->selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta untuk pengiriman undangan masal.');

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

            if (empty($participant->qr_code_path) || !file_exists(public_path('uploads/qr_code/' . $participant->qr_code_path))) {
                Participant::generateQrCodeFile($participant);
            }

            try {
                Mail::to($participant->email)->send(new \App\Mail\InvitationTicketMail($participant));

                $update = [];
                if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_sent_at')) {
                    $update['invitation_sent_at'] = now();
                    $update['invitation_status'] = 'sent';
                }
                if (!empty($update)) {
                    $participant->update($update);
                }

                $sentCount++;
            } catch (\Throwable $e) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_status')) {
                    $participant->update(['invitation_status' => 'failed']);
                }
                $failedCount++;
            }
        }

        $this->selectedParticipants = [];
        $this->selectAll = false;

        session()->flash('success', "Pengiriman undangan masal selesai: {$sentCount} tiket barcode berhasil dikirim.".($failedCount > 0 ? " ({$failedCount} dilewati/gagal karena tanpa email)" : ''));
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

    /**
     * Unduh berkas PDF masal (Sertifikat atau Kwitansi) untuk peserta yang dipilih dalam format .ZIP
     */
    public function downloadBulkPdf(string $type = 'sertifikat')
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh dokumen.'
        );

        if (empty($this->selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta untuk mengunduh berkas masal.');

            return null;
        }

        $participants = Participant::whereIn('id', $this->selectedParticipants)->get();
        $service = app(\App\Services\ParticipantDocumentService::class);
        $zipPath = $service->buildBulkZip($participants, $type);

        if (! $zipPath || ! file_exists($zipPath)) {
            session()->flash('error', 'Gagal membuat file ZIP dokumen masal.');

            return null;
        }

        $label = match ($type) {
            'kwitansi' => 'Kwitansi',
            'blank'    => 'Sertifikat_Blank',
            default    => 'Sertifikat_Warna',
        };
        $downloadName = "Bulk_{$label}_Linkpemda_" . date('Ymd_His') . ".zip";

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Unduh berkas PDF Absensi / Daftar Hadir resmi masal untuk peserta yang dipilih.
     */
    public function downloadBulkAbsensi()
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh dokumen.'
        );

        if (empty($this->selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta untuk mengunduh absensi.');

            return null;
        }

        $participants = Participant::whereIn('id', $this->selectedParticipants)
            ->orderBy('name')
            ->get();

        if ($participants->isEmpty()) {
            session()->flash('error', 'Data peserta yang dipilih tidak ditemukan.');

            return null;
        }

        $service = app(\App\Services\ParticipantDocumentService::class);
        $pdfBinary = $service->generateAbsensiPdf($participants);

        $firstP = $participants->first();
        $cleanTheme = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::limit($firstP->theme ?: 'Kegiatan', 25), '_');
        $downloadName = "Absensi_Linkpemda_{$cleanTheme}_" . date('Ymd_His') . ".pdf";

        return response()->streamDownload(function () use ($pdfBinary) {
            echo $pdfBinary;
        }, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function showQrModal(string $id): void
    {
        $participant = Participant::findOrFail($id);
        $this->previewQrUrl = $participant->qr_code_url;
        $this->previewQrTitle = "{$participant->name} ({$participant->certificate_number})";

        $this->dispatch('open-qr-modal');
    }

    /**
     * Mengembalikan URL route PDF untuk peserta tertentu.
     * Dipanggil dari blade: @php($this->pdfRoute('sertifikat', 'view', $participant->id))
     */
    public function getPdfRoute(string $type, string $action, string $participantId): string
    {
        $participant = Participant::find($participantId);
        if (! $participant) {
            return '#';
        }

        $routeMap = [
            'sertifikat' => [
                'view'           => 'admin.participant.linkpemda.certificate.view',
                'download'       => 'admin.participant.linkpemda.certificate.download',
                'print'          => 'admin.participant.linkpemda.certificate.print',
                'download-blank' => 'admin.participant.linkpemda.certificate.download-blank',
            ],
            'kwitansi' => [
                'view'     => 'admin.participant.linkpemda.kwitansi.view',
                'download' => 'admin.participant.linkpemda.kwitansi.download',
            ],
        ];

        $routeName = $routeMap[$type][$action] ?? null;

        return $routeName ? route($routeName, $participant) : '#';
    }

    protected function getFilteredQuery(bool $withEmailStatus = true)
    {
        return Participant::query()
            ->search($this->search)
            ->when($withEmailStatus && $this->selectedEmailStatus !== '', function ($q) {
                $q->where('email_status', $this->selectedEmailStatus);
            })
            ->when($this->selectedMonth !== '', function ($q) {
                $monthNum = (int) $this->selectedMonth;
                $romanMonths = [
                    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
                ];
                $indonesianMonths = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];

                $roman = $romanMonths[$monthNum] ?? '';
                $indoName = $indonesianMonths[$monthNum] ?? '';

                $q->where(function ($sub) use ($monthNum, $roman, $indoName) {
                    $sub->whereMonth('created_at', $monthNum);
                    if ($roman) {
                        $sub->orWhere('certificate_number', 'like', "%-{$roman}-%");
                    }
                    if ($indoName) {
                        $sub->orWhere('certificate_date', 'like', "%{$indoName}%")
                            ->orWhere('registration_date', 'like', "%{$indoName}%")
                            ->orWhere('receipt_date', 'like', "%{$indoName}%");
                    }
                });
            })
            ->when($this->selectedYear !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereYear('created_at', $this->selectedYear)
                        ->orWhere('certificate_number', 'like', "%-{$this->selectedYear}")
                        ->orWhere('certificate_date', 'like', "%{$this->selectedYear}%")
                        ->orWhere('registration_date', 'like', "%{$this->selectedYear}%")
                        ->orWhere('receipt_date', 'like', "%{$this->selectedYear}%");
                });
            });
    }

    protected function getQuery()
    {
        return $this->getFilteredQuery(true)->latest('created_at');
    }

    public function render()
    {
        $participants = $this->getQuery()->paginate($this->perPage);

        // Daftar tahun untuk filter
        $currentYear = (int) now()->format('Y');
        $years = range($currentYear, $currentYear - 4);

        // Daftar bulan untuk filter
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Statistik ringkas & nominal kwitansi dinamis mengikuti filter yang aktif
        $filteredQuery = $this->getFilteredQuery(true);
        $totalParticipants = (clone $filteredQuery)->count();
        $totalAmount = (clone $filteredQuery)->sum('amount');

        // Jika filter status email aktif, hitung sesuai filter tersebut; jika semua status, hitung dari query terfilter
        if ($this->selectedEmailStatus !== '') {
            $emailSentCount = $this->selectedEmailStatus === 'sent' ? $totalParticipants : 0;
            $emailPendingCount = $this->selectedEmailStatus === 'pending' ? $totalParticipants : 0;
        } else {
            $baseQuery = $this->getFilteredQuery(false);
            $emailSentCount = (clone $baseQuery)->where('email_status', 'sent')->count();
            $emailPendingCount = (clone $baseQuery)->where('email_status', 'pending')->count();
        }

        return view('livewire.admin.participant.index', [
            'participants' => $participants,
            'years' => $years,
            'months' => $months,
            'totalParticipants' => $totalParticipants,
            'emailSentCount' => $emailSentCount,
            'emailPendingCount' => $emailPendingCount,
            'totalAmount' => $totalAmount,
        ]);
    }
}

