<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Services\ParticipantDocumentService;

/**
 * Controller untuk generate & stream dokumen PDF peserta (sertifikat & kwitansi).
 * Route ini diakses via link biasa (target="_blank") agar PDF terbuka di tab baru.
 */
class ParticipantDocumentController extends Controller
{
    public function __construct(
        protected ParticipantDocumentService $documentService
    ) {}

    // =========================================================================
    // Sertifikat
    // =========================================================================

    /** Tampilkan sertifikat (inline preview di tab baru) */
    public function viewCertificate(Participant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat sertifikat.'
        );

        return $this->documentService->streamInline($participant, 'default');
    }

    /** Unduh sertifikat (save-as / download) */
    public function downloadCertificate(Participant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh sertifikat.'
        );

        return $this->documentService->streamDownload($participant, 'default');
    }

    /** Print sertifikat blank (tanpa background warna, untuk cetak di atas kertas sertifikat) */
    public function printCertificate(Participant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mencetak sertifikat.'
        );

        return $this->documentService->streamInline($participant, 'blank');
    }

    /** Unduh sertifikat blank (save-as PDF blank tanpa background) */
    public function downloadCertificateBlank(Participant $participant)
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

    /** Tampilkan kwitansi (inline preview di tab baru) */
    public function viewKwitansi(Participant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat kwitansi.'
        );

        return $this->documentService->streamInline($participant, 'kwitansi');
    }

    /** Unduh kwitansi (save-as / download) */
    public function downloadKwitansi(Participant $participant)
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh kwitansi.'
        );

        return $this->documentService->streamDownload($participant, 'kwitansi');
    }
}
