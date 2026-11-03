<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrenParticipant;
use App\Services\TrenParticipantDocumentService;

/**
 * Controller untuk generate & stream dokumen PDF peserta Tren Media Diklat (sertifikat & kwitansi).
 * Route ini diakses via link biasa (target="_blank") agar PDF terbuka di tab baru.
 */
class TrenParticipantDocumentController extends Controller
{
    public function __construct(
        protected TrenParticipantDocumentService $documentService
    ) {}

    // =========================================================================
    // Sertifikat
    // =========================================================================

    /** Tampilkan sertifikat Tren Media (inline preview di tab baru) */
    public function viewCertificate(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat sertifikat.'
        );

        return $this->documentService->streamInline($participant, 'default');
    }

    /** Unduh sertifikat Tren Media (save-as / download) */
    public function downloadCertificate(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh sertifikat.'
        );

        return $this->documentService->streamDownload($participant, 'default');
    }

    /** Print sertifikat blank Tren Media (tanpa background warna) */
    public function printCertificate(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mencetak sertifikat.'
        );

        return $this->documentService->streamInline($participant, 'blank');
    }

    /** Unduh sertifikat blank Tren Media (save-as PDF blank tanpa background) */
    public function downloadCertificateBlank(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh sertifikat blank.'
        );

        return $this->documentService->streamDownload($participant, 'blank');
    }

    // =========================================================================
    // Kwitansi
    // =========================================================================

    /** Tampilkan kwitansi Tren Media (inline preview di tab baru) */
    public function viewKwitansi(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat kwitansi.'
        );

        return $this->documentService->streamInline($participant, 'kwitansi');
    }

    /** Unduh kwitansi Tren Media (save-as / download) */
    public function downloadKwitansi(TrenParticipant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh kwitansi.'
        );

        return $this->documentService->streamDownload($participant, 'kwitansi');
    }
}

