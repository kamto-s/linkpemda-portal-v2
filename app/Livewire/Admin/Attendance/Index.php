<?php

namespace App\Livewire\Admin\Attendance;

use App\Models\Participant;
use App\Services\AttendanceService;
use App\Services\ParticipantDocumentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Absensi Kegiatan - Linkpemda')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Absensi';

    // Filter Kegiatan
    public string $selectedTheme = '';

    // Sesi aktif saat ini: 'registrasi', 'hari_1', 'hari_2', dst.
    public string $activeSession = 'registrasi';

    // Pencarian cepat
    public string $search = '';

    // Filter status kehadiran di sesi ini: 'all', 'hadir', 'belum_hadir', 'izin', 'sakit'
    public string $statusFilter = 'all';

    public int $perPage = 15;

    // Checkbox seleksi masal
    public array $selectedParticipants = [];
    public bool $selectAll = false;

    // Modal Status Manual
    public ?string $modalParticipantId = null;
    public string $modalParticipantName = '';
    public string $modalStatus = 'hadir';
    public string $modalNotes = '';

    public function mount(): void
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk membuka halaman absensi.'
        );

        // Pilih tema kegiatan yang sedang berlangsung hari ini atau yang terdekat
        if (empty($this->selectedTheme)) {
            $events = $this->eventList;
            $first = $events->first(fn($e) => $e->is_today) ?? $events->first();
            if ($first) {
                $this->selectedTheme = $first->theme;
            }
        }
    }

    public function updatedSelectedTheme(): void
    {
        $this->resetPage();
        $this->selectedParticipants = [];
        $this->selectAll = false;

        // Reset sesi ke 'registrasi' jika tema berganti
        $this->activeSession = 'registrasi';
    }

    public function updatedActiveSession(): void
    {
        $this->resetPage();
        $this->selectedParticipants = [];
        $this->selectAll = false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedParticipants = $this->getFilteredParticipantQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedParticipants = [];
        }
    }

    /**
     * Dapatkan daftar kegiatan yang sedang aktif (berlangsung hari ini) atau akan datang.
     * Kegiatan yang tanggal pelaksanaannya sudah selesai / lewat TIDAK DITAMPILKAN SAMA SEKALI.
     */
    public function getEventListProperty()
    {
        $docService = app(ParticipantDocumentService::class);
        $events = Participant::whereNotNull('theme')
            ->where('theme', '!=', '')
            ->selectRaw('theme, MAX(certificate_date) as certificate_date, MAX(created_at) as latest_created_at, COUNT(*) as total_participants')
            ->groupBy('theme')
            ->orderByDesc('latest_created_at')
            ->get();

        $now = \Carbon\Carbon::now()->startOfDay();

        return $events->filter(function ($event) use ($docService, $now) {
            $sched = $docService->parseAbsensiSchedule($event->certificate_date ?? '');
            $endDateStr = $sched['end_date'] ?? null;
            $startDateStr = $sched['start_date'] ?? null;

            // Jika tanggal tidak dapat diparsing, jangan tampilkan di halaman absensi
            if (empty($endDateStr)) {
                return false;
            }

            try {
                $endDate = \Carbon\Carbon::parse($endDateStr)->endOfDay();

                // JIKA TANGGAL AKHIR KEGIATAN SUDAH LEWAT DARI HARI INI -> 100% TIDAK MUNCUL!
                if ($now->isAfter($endDate)) {
                    return false;
                }

                $startDate = !empty($startDateStr) ? \Carbon\Carbon::parse($startDateStr)->startOfDay() : $endDate;
                $event->is_today = $now->betweenIncluded($startDate, $endDate);
                $event->status_label = $event->is_today ? 'Hari Ini' : 'Akan Datang';

                return true;
            } catch (\Throwable) {
                return false;
            }
        })->values();
    }

    /**
     * Dapatkan daftar sesi kegiatan dinamis berdasarkan tanggal kegiatan terpilih.
     */
    public function getSessionsProperty(AttendanceService $attendanceService): array
    {
        $sample = !empty($this->selectedTheme)
            ? Participant::where('theme', $this->selectedTheme)->first()
            : null;

        $dateStr = $sample ? (string) ($sample->certificate_date ?: ($sample->registration_date ?? '')) : '';

        return $attendanceService->getEventSessions($dateStr);
    }

    /**
     * Query dasar peserta yang terfilter berdasarkan tema kegiatan.
     */
    protected function getFilteredParticipantQuery()
    {
        if (empty($this->selectedTheme)) {
            return Participant::whereRaw('1 = 0');
        }

        $query = Participant::with([
            'attendances' => function ($q) {
                $q->where('session_type', $this->activeSession);
            }
        ])->where('theme', $this->selectedTheme);

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('institution_name', 'like', $term)
                    ->orWhere('certificate_number', 'like', $term)
                    ->orWhere('position', 'like', $term);
            });
        }

        if ($this->statusFilter === 'hadir') {
            $query->whereHas('attendances', function ($q) {
                $q->where('session_type', $this->activeSession)->where('status', 'hadir');
            });
        } elseif ($this->statusFilter === 'belum_hadir') {
            $query->whereDoesntHave('attendances', function ($q) {
                $q->where('session_type', $this->activeSession);
            });
        } elseif (in_array($this->statusFilter, ['izin', 'sakit', 'alpha'])) {
            $query->whereHas('attendances', function ($q) {
                $q->where('session_type', $this->activeSession)->where('status', $this->statusFilter);
            });
        }

        return $query->orderBy('name');
    }

    /**
     * Check-in manual 1 klik.
     */
    public function checkIn(string $participantId, AttendanceService $attendanceService): void
    {
        $participant = Participant::findOrFail($participantId);
        $attendanceService->recordCheckIn($participant, $this->activeSession, auth()->id());

        $this->dispatch('notify-success', message: "{$participant->name} berhasil Check-in.");
    }

    /**
     * Check-out manual 1 klik.
     */
    public function checkOut(string $participantId, AttendanceService $attendanceService): void
    {
        $participant = Participant::findOrFail($participantId);
        $attendanceService->recordCheckOut($participant, $this->activeSession);

        $this->dispatch('notify-success', message: "{$participant->name} berhasil Check-out.");
    }

    /**
     * Toggle Check-in/Check-out cerdas.
     */
    public function toggleCheckIn(string $participantId, AttendanceService $attendanceService): void
    {
        $participant = Participant::findOrFail($participantId);
        $result = $attendanceService->toggleCheckIn($participant, $this->activeSession, auth()->id());

        $this->dispatch('notify-success', message: $result['message']);
    }

    /**
     * Buka modal edit status kehadiran manual.
     */
    public function openStatusModal(string $participantId): void
    {
        $participant = Participant::with([
            'attendances' => function ($q) {
                $q->where('session_type', $this->activeSession);
            }
        ])->findOrFail($participantId);

        $this->modalParticipantId = $participant->id;
        $this->modalParticipantName = $participant->name;

        $existing = $participant->attendances->first();
        $this->modalStatus = $existing?->status ?? 'hadir';
        $this->modalNotes = $existing?->notes ?? '';

        $this->dispatch('open-modal', modal: 'modalEditAttendanceStatus');
    }

    /**
     * Simpan status kehadiran manual dari modal.
     */
    public function saveStatusModal(AttendanceService $attendanceService): void
    {
        if (!$this->modalParticipantId) {
            return;
        }

        $participant = Participant::findOrFail($this->modalParticipantId);
        $attendanceService->updateStatus(
            $participant,
            $this->activeSession,
            $this->modalStatus,
            $this->modalNotes ?: null,
            auth()->id()
        );

        $this->dispatch('close-modal', modal: 'modalEditAttendanceStatus');
        $this->dispatch('notify-success', message: "Status absensi {$participant->name} berhasil diperbarui.");
    }

    /**
     * Reset / batalkan absensi peserta di sesi ini.
     */
    public function resetAttendance(string $participantId, AttendanceService $attendanceService): void
    {
        $participant = Participant::findOrFail($participantId);
        $attendanceService->resetAttendance($participant, $this->activeSession);

        $this->dispatch('notify-info', message: "Absensi {$participant->name} dibatalkan.");
    }

    /**
     * Check-in masal untuk peserta terpilih.
     */
    public function bulkCheckIn(AttendanceService $attendanceService): void
    {
        if (empty($this->selectedParticipants)) {
            $this->dispatch('notify-error', message: "Pilih minimal 1 peserta.");
            return;
        }

        $participants = Participant::whereIn('id', $this->selectedParticipants)->get();
        foreach ($participants as $p) {
            $attendanceService->recordCheckIn($p, $this->activeSession, auth()->id());
        }

        $count = count($this->selectedParticipants);
        $this->selectedParticipants = [];
        $this->selectAll = false;

        $this->dispatch('notify-success', message: "{$count} peserta berhasil di-Check-in masal.");
    }

    /**
     * Scan kode QR (dipanggil dari kamera scanner JavaScript).
     */
    public function scanCode(string $code, AttendanceService $attendanceService): void
    {
        $participant = $attendanceService->resolveParticipantFromCode($code);

        if (!$participant) {
            $this->dispatch('scan-result-error', message: "Peserta tidak ditemukan untuk kode: {$code}");
            return;
        }

        $result = $attendanceService->toggleCheckIn($participant, $this->activeSession, auth()->id());

        $this->dispatch('scan-result-success', [
            'name' => $participant->name,
            'institution' => $participant->institution_name ?: '-',
            'action' => $result['action'],
            'message' => $result['message'],
        ]);
    }

    /**
     * Unduh PDF Absensi resmi untuk kegiatan ini.
     */
    public function downloadPdf(ParticipantDocumentService $docService)
    {
        if (empty($this->selectedTheme)) {
            $this->dispatch('notify-error', message: "Silakan pilih kegiatan terlebih dahulu untuk mengunduh PDF absensi.");
            return null;
        }

        $participants = Participant::where('theme', $this->selectedTheme)
            ->orderBy('name')
            ->get();

        if ($participants->isEmpty()) {
            $this->dispatch('notify-error', message: "Tidak ada data peserta untuk diunduh.");
            return null;
        }

        $pdfBinary = $docService->generateAbsensiPdf($participants);
        $cleanTheme = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::limit($this->selectedTheme, 25), '_');
        $fileName = "Absensi_Linkpemda_{$cleanTheme}_" . date('Ymd_His') . ".pdf";

        return response()->streamDownload(function () use ($pdfBinary) {
            echo $pdfBinary;
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        $query = $this->getFilteredParticipantQuery();

        // Hitung statistik untuk sesi ini
        $baseEventQuery = Participant::query();
        if (!empty($this->selectedTheme)) {
            $baseEventQuery->where('theme', $this->selectedTheme);
        }
        $totalEvent = (clone $baseEventQuery)->count();

        $totalHadir = (clone $baseEventQuery)
            ->whereHas('attendances', function ($q) {
                $q->where('session_type', $this->activeSession)->where('status', 'hadir');
            })->count();

        $totalIzin = (clone $baseEventQuery)
            ->whereHas('attendances', function ($q) {
                $q->where('session_type', $this->activeSession)->whereIn('status', ['izin', 'sakit']);
            })->count();

        $totalBelum = max(0, $totalEvent - $totalHadir - $totalIzin);

        $participants = $query->paginate($this->perPage);

        return view('livewire.admin.attendance.index', [
            'participants' => $participants,
            'totalEvent' => $totalEvent,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'totalBelum' => $totalBelum,
        ]);
    }
}

