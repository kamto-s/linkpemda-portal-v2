<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Participant;
use Carbon\Carbon;

class AttendanceService
{
    public function __construct(
        protected ParticipantDocumentService $docService
    ) {}

    /**
     * Cari peserta berdasarkan kode scan (nomor sertifikat, URL verifikasi, atau ID).
     */
    public function resolveParticipantFromCode(string $code): ?Participant
    {
        $code = trim($code);
        if (empty($code)) {
            return null;
        }

        // 1. Jika kode berupa URL verifikasi penuh (misal hasil scan kamera bawaan)
        if (filter_var($code, FILTER_VALIDATE_URL)) {
            $path = parse_url($code, PHP_URL_PATH) ?? '';
            // Ekstrak segmen terakhir dari /verify/{number} atau /sertifikat/status/{number}
            if (preg_match('~/(?:verify|status)/([^/?#]+)~i', $path, $m)) {
                $code = urldecode($m[1]);
            } else {
                $segments = array_filter(explode('/', $path));
                $code = urldecode(end($segments) ?: $code);
            }
        }

        // 2. Cari berdasarkan nomor sertifikat
        $participant = Participant::where('certificate_number', $code)->first();
        if ($participant) {
            return $participant;
        }

        // 3. Cari berdasarkan UUID ID
        if (\Illuminate\Support\Str::isUuid($code)) {
            $participant = Participant::find($code);
            if ($participant) {
                return $participant;
            }
        }

        // 4. Cari berdasarkan nomor telepon jika format cocok
        return Participant::where('phone', $code)->first();
    }

    /**
     * Catat Check-in untuk peserta pada sesi tertentu.
     */
    public function recordCheckIn(Participant $participant, string $sessionType, ?string $userId = null): Attendance
    {
        return Attendance::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'session_type' => $sessionType,
            ],
            [
                'check_in_at' => Carbon::now(),
                'status' => 'hadir',
                'verified_by' => $userId,
            ]
        );
    }

    /**
     * Catat Check-out untuk peserta pada sesi tertentu.
     */
    public function recordCheckOut(Participant $participant, string $sessionType): Attendance
    {
        $attendance = Attendance::firstOrNew([
            'participant_id' => $participant->id,
            'session_type' => $sessionType,
        ]);

        if (!$attendance->exists) {
            $attendance->check_in_at = Carbon::now();
            $attendance->status = 'hadir';
        }

        $attendance->check_out_at = Carbon::now();
        $attendance->save();

        return $attendance;
    }

    /**
     * Check-in / Check-out cerdas (Scan pertama = Check-in, Scan kedua = Check-out).
     */
    public function toggleCheckIn(Participant $participant, string $sessionType, ?string $userId = null): array
    {
        $existing = Attendance::where('participant_id', $participant->id)
            ->where('session_type', $sessionType)
            ->first();

        if (!$existing || empty($existing->check_in_at)) {
            $att = $this->recordCheckIn($participant, $sessionType, $userId);
            return [
                'action' => 'check_in',
                'attendance' => $att,
                'message' => "Check-in berhasil: {$participant->name}",
            ];
        }

        if (empty($existing->check_out_at)) {
            $att = $this->recordCheckOut($participant, $sessionType);
            return [
                'action' => 'check_out',
                'attendance' => $att,
                'message' => "Check-out berhasil: {$participant->name}",
            ];
        }

        return [
            'action' => 'already_completed',
            'attendance' => $existing,
            'message' => "{$participant->name} sudah lengkap Check-in dan Check-out pada sesi ini.",
        ];
    }

    /**
     * Perbarui status kehadiran manual (Hadir, Izin, Sakit, Alpha).
     */
    public function updateStatus(
        Participant $participant,
        string $sessionType,
        string $status,
        ?string $notes = null,
        ?string $userId = null
    ): Attendance {
        return Attendance::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'session_type' => $sessionType,
            ],
            [
                'status' => $status,
                'check_in_at' => $status === 'hadir' ? Carbon::now() : null,
                'notes' => $notes,
                'verified_by' => $userId,
            ]
        );
    }

    /**
     * Reset / batalkan kehadiran peserta pada sesi tertentu.
     */
    public function resetAttendance(Participant $participant, string $sessionType): bool
    {
        return (bool) Attendance::where('participant_id', $participant->id)
            ->where('session_type', $sessionType)
            ->delete();
    }

    /**
     * Ekstrak daftar sesi yang tersedia untuk suatu kegiatan (Registrasi, Hari 1, Hari 2, dst).
     */
    public function getEventSessions(string $dateStr): array
    {
        $sched = $this->docService->parseAbsensiSchedule($dateStr);
        $daysCount = min(7, max(1, (int) ($sched['days_count'] ?? 1)));

        $firstDayDetail = $sched['days_detail'][1] ?? null;
        $firstDateBrief = !empty($firstDayDetail['tanggal_singkat']) ? $firstDayDetail['tanggal_singkat'] : '';

        $sessions = [
            'registrasi' => [
                'key' => 'registrasi',
                'label' => 'Registrasi',
                'sub' => $firstDateBrief ? "({$firstDateBrief})" : '',
                'full_label' => 'Registrasi' . ($firstDateBrief ? " ({$firstDateBrief})" : ''),
                'is_last' => false,
            ],
        ];

        for ($d = 1; $d <= $daysCount; $d++) {
            $dayDetail = $sched['days_detail'][$d] ?? null;
            $dayDateBrief = !empty($dayDetail['tanggal_singkat']) ? $dayDetail['tanggal_singkat'] : '';
            $key = "hari_{$d}";

            $sessions[$key] = [
                'key' => $key,
                'label' => "Hari {$d}",
                'sub' => $dayDateBrief ? "({$dayDateBrief})" : '',
                'full_label' => "Hari {$d}" . ($dayDateBrief ? " ({$dayDateBrief})" : ''),
                'is_last' => ($d === $daysCount),
            ];
        }

        return $sessions;
    }

    /**
     * Cek status keabsahan sertifikat peserta:
     * - Peserta data lama / masa lalu (event sudah lewat): 100% VALID otomatis (garansi kompatibilitas mundur).
     * - Peserta kegiatan berjalan: Valid jika sudah hadir di hari penutupan (hari terakhir).
     * - Jika belum hadir di hari terakhir dan kegiatan sedang berlangsung/akan datang: Status edukatif 'in_progress'
     *   (Peserta terdaftar resmi, sertifikat akan tervalidasi setelah menyelesaikan absensi di hari terakhir).
     */
    public function checkCertificateValidity(Participant $participant): array
    {
        $sched = $this->docService->parseAbsensiSchedule($participant->certificate_date ?? '');
        $endDateStr = $sched['end_date'] ?? null;
        $daysCount = $sched['days_count'] ?? 1;

        // Ambil data absensi peserta secara aman (tahan error jika tabel belum dimigrasi)
        $attendedLastDay = false;
        $hasCheckedInAny = false;
        try {
            $attendances = $participant->relationLoaded('attendances')
                ? $participant->attendances
                : $participant->attendances()->get();

            $lastDayKey = "hari_{$daysCount}";
            $attendedLastDay = $attendances->first(function ($att) use ($lastDayKey) {
                return $att->session_type === $lastDayKey && $att->status === 'hadir';
            }) !== null;

            $hasCheckedInAny = $attendances->where('status', 'hadir')->isNotEmpty();
        } catch (\Throwable) {
            // Aman jika tabel attendances belum dimigrasi
        }

        // 1. Cek apakah tanggal acara sudah berlalu di masa lalu (sebelum hari ini)
        $isPastEvent = false;
        if (!empty($endDateStr)) {
            try {
                $endDate = Carbon::parse($endDateStr)->endOfDay();
                if (Carbon::now()->isAfter($endDate)) {
                    $isPastEvent = true;
                }
            } catch (\Throwable) {
                $isPastEvent = true; // Fallback aman jika format tanggal lama tidak standar
            }
        } else {
            // Jika tanggal tidak dapat diparsing, anggap valid (data migrasi / lama)
            $isPastEvent = true;
        }

        // 2. Jika acara masa lampau ATAU peserta sudah hadir di hari terakhir:
        if ($isPastEvent || $attendedLastDay) {
            return [
                'is_valid' => true,
                'status_code' => $attendedLastDay ? 'valid_attended' : 'valid_completed',
                'status_title' => 'Sertifikat Resmi Terverifikasi',
                'status_desc' => $attendedLastDay
                    ? 'Peserta telah melengkapi seluruh rangkaian kehadiran kegiatan dan sertifikat dinyatakan sah.'
                    : 'Data terdaftar resmi dalam pangkalan data sistem Linkpemda.',
                'badge_color' => 'success',
                'is_in_progress' => false,
                'attended_last_day' => $attendedLastDay,
                'is_past_event' => $isPastEvent,
            ];
        }

        // 3. Jika acara sedang berlangsung / akan datang dan belum absen hari terakhir:
        return [
            'is_valid' => false,
            'status_code' => 'in_progress',
            'status_title' => 'Peserta Terdaftar (Menunggu Hari Penutupan)',
            'status_desc' => 'Data peserta terdaftar sah di sistem Linkpemda. Sertifikat digital resmi akan aktif tervalidasi setelah peserta menyelesaikan absensi di hari terakhir kegiatan.',
            'badge_color' => 'warning',
            'is_in_progress' => true,
            'attended_last_day' => false,
            'is_past_event' => false,
            'has_checked_in' => $hasCheckedInAny,
        ];
    }
}
