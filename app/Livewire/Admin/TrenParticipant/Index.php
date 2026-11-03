<?php

namespace App\Livewire\Admin\TrenParticipant;

use App\Models\TrenParticipant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Data Peserta Tren Media')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Data Peserta Tren Media';

    public string $search = '';

    public string $selectedMonth = '';

    public string $selectedYear = '';

    public int $perPage = 10;

    // Checkbox seleksi masal
    public array $selectedParticipants = [];

    public bool $selectAll = false;

    // Delete confirmation modal state
    public ?string $participantToDeleteId = null;

    public string $participantToDeleteName = '';

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

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedMonth', 'selectedYear', 'selectedParticipants', 'selectAll']);
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
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function confirmDelete(string $id): void
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        $participant = TrenParticipant::findOrFail($id);
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
        if (!$targetId) {
            return;
        }

        $participant = TrenParticipant::findOrFail($targetId);
        $name = $participant->name;
        $participant->delete();

        session()->flash('success', "Data peserta \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['participantToDeleteId', 'participantToDeleteName']);
    }

    public function deleteBulk(): void
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        if (empty($this->selectedParticipants)) {
            session()->flash('error', 'Pilih minimal 1 peserta untuk dihapus.');

            return;
        }

        $count = count($this->selectedParticipants);
        TrenParticipant::whereIn('id', $this->selectedParticipants)->delete();

        $this->selectedParticipants = [];
        $this->selectAll = false;

        session()->flash('success', "Sebanyak {$count} data peserta berhasil dihapus.");
    }

    /**
     * Unduh berkas PDF masal (Sertifikat atau Kwitansi) untuk peserta Tren yang dipilih dalam format .ZIP
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

        $participants = TrenParticipant::whereIn('id', $this->selectedParticipants)->get();
        $service = app(\App\Services\TrenParticipantDocumentService::class);
        $zipPath = $service->buildBulkZip($participants, $type);

        if (!$zipPath || !file_exists($zipPath)) {
            session()->flash('error', 'Gagal membuat file ZIP dokumen masal.');

            return null;
        }

        $label = match ($type) {
            'kwitansi' => 'Kwitansi',
            'blank' => 'Sertifikat_Blank',
            default => 'Sertifikat_Warna',
        };
        $downloadName = "Bulk_{$label}_TrenMedia_" . date('Ymd_His') . ".zip";

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Mengembalikan URL route PDF untuk peserta tertentu.
     */
    public function getPdfRoute(string $type, string $action, string $participantId): string
    {
        $participant = TrenParticipant::find($participantId);
        if (!$participant) {
            return '#';
        }

        $routeMap = [
            'sertifikat' => [
                'view' => 'admin.participant.trendiklat.certificate.view',
                'download' => 'admin.participant.trendiklat.certificate.download',
                'print' => 'admin.participant.trendiklat.certificate.print',
                'download-blank' => 'admin.participant.trendiklat.certificate.download-blank',
            ],
            'kwitansi' => [
                'view' => 'admin.participant.trendiklat.kwitansi.view',
                'download' => 'admin.participant.trendiklat.kwitansi.download',
            ],
        ];

        $routeName = $routeMap[$type][$action] ?? null;

        return $routeName ? route($routeName, $participant) : '#';
    }

    protected function getFilteredQuery()
    {
        return TrenParticipant::query()
            ->search($this->search)
            ->when($this->selectedMonth !== '', function ($q) {
                $monthNum = (int) $this->selectedMonth;
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
                $indonesianMonths = [
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

                $roman = $romanMonths[$monthNum] ?? '';
                $indoName = $indonesianMonths[$monthNum] ?? '';

                $q->where(function ($sub) use ($monthNum, $roman, $indoName) {
                    $sub->whereMonth('created_at', $monthNum);
                    if ($roman) {
                        $sub->orWhere('certificate_number', 'like', "%/{$roman}/%");
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
                        ->orWhere('certificate_number', 'like', "%/{$this->selectedYear}")
                        ->orWhere('certificate_date', 'like', "%{$this->selectedYear}%")
                        ->orWhere('registration_date', 'like', "%{$this->selectedYear}%")
                        ->orWhere('receipt_date', 'like', "%{$this->selectedYear}%");
                });
            });
    }

    protected function getQuery()
    {
        return $this->getFilteredQuery()->latest('created_at');
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

        $filteredQuery = $this->getFilteredQuery();
        $totalParticipants = (clone $filteredQuery)->count();
        $totalAmount = (clone $filteredQuery)->sum('amount');

        return view('livewire.admin.tren-participant.index', [
            'participants' => $participants,
            'years' => $years,
            'months' => $months,
            'totalParticipants' => $totalParticipants,
            'totalAmount' => $totalAmount,
        ]);
    }
}
