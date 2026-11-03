<?php

namespace App\Services;

use App\Models\TrenParticipant;
use setasign\Fpdi\Fpdi;

/**
 * Service untuk pembuatan dokumen PDF (Sertifikat & Kwitansi)
 * untuk peserta Tren Media Diklat.
 * Menggunakan template di folder public/assets/pdf-templates/trenmedia/
 * Tanpa QR code dan teks Scan Here sesuai kebutuhan saat ini.
 */
class TrenParticipantDocumentService
{
    /**
     * Menyiapkan instance FPDI berdasarkan template & tipe dokumen, lalu merender
     * teks data peserta di atas template PDF Tren Media Diklat.
     *
     * @param  string  $type  'default' = sertifikat warna | 'blank' = sertifikat blank | 'kwitansi'
     */
    public function buildPdf(TrenParticipant $participant, string $type = 'default'): Fpdi
    {
        $templatePath = match ($type) {
            'blank'    => public_path('assets/pdf-templates/trenmedia/sertifikat_blank.pdf'),
            'kwitansi' => public_path('assets/pdf-templates/trenmedia/kwitansi.pdf'),
            default    => public_path('assets/pdf-templates/trenmedia/sertifikat.pdf'),
        };

        // Fallback jika nama file memiliki variasi '_tren' atau ada di linkpemda
        if (! file_exists($templatePath)) {
            $fallback = match ($type) {
                'blank'    => file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat_blank_tren.pdf'))
                                ? public_path('assets/pdf-templates/trenmedia/sertifikat_blank_tren.pdf')
                                : public_path('assets/pdf-templates/trenmedia/sertifikat.pdf'),
                'kwitansi' => public_path('assets/pdf-templates/linkpemda/kwitansi.pdf'),
                default    => file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat_tren.pdf'))
                                ? public_path('assets/pdf-templates/trenmedia/sertifikat_tren.pdf')
                                : public_path('assets/pdf-templates/linkpemda/sertifikat.pdf'),
            };
            if (file_exists($fallback)) {
                $templatePath = $fallback;
            }
        }

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->setSourceFile($templatePath);
        $template = $pdf->importPage(1);
        $size     = $pdf->getTemplateSize($template);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);

        if ($type === 'kwitansi') {
            $this->renderKwitansi($pdf, $participant);
        } else {
            $this->renderSertifikat($pdf, $participant);
        }

        return $pdf;
    }

    /**
     * Stream PDF langsung ke browser (inline / tampil di tab baru).
     */
    public function streamInline(TrenParticipant $participant, string $type = 'default'): \Illuminate\Http\Response
    {
        $pdf      = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);
        $content  = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Stream PDF sebagai unduhan (download / save-as).
     */
    public function streamDownload(TrenParticipant $participant, string $type = 'default'): \Illuminate\Http\Response
    {
        $pdf      = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);
        $content  = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Mengembalikan raw binary string dokumen PDF.
     */
    public function getPdfBinary(TrenParticipant $participant, string $type = 'default'): string
    {
        $pdf      = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);

        return $pdf->Output($filename, 'S');
    }

    /**
     * Membuat file ZIP di direktori temporary yang berisi kumpulan file PDF peserta Tren Media.
     */
    public function buildBulkZip(iterable $participants, string $type = 'sertifikat'): ?string
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'tmd_docs_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $pdfType = match ($type) {
            'kwitansi' => 'kwitansi',
            'blank'    => 'blank',
            default    => 'default',
        };

        foreach ($participants as $participant) {
            try {
                $binary = $this->getPdfBinary($participant, $pdfType);
                $filename = $this->buildFilename($participant, $pdfType);
                $zip->addFromString($filename, $binary);
            } catch (\Throwable) {
                // Lewati jika terjadi kegagalan pada record tertentu
            }
        }

        $zip->close();

        return file_exists($zipPath) ? $zipPath : null;
    }

    // =========================================================================
    // Render: Sertifikat Tren Media Diklat
    // =========================================================================

    protected function renderSertifikat(Fpdi $pdf, TrenParticipant $participant): void
    {
        $safeCert = str_replace('/', '_', $participant->certificate_number ?? '');
        $pdf->SetTitle('Sertifikat_' . $participant->name . ' (' . $safeCert . ')');

        // Nama Peserta
        $pdf->SetFont('Arial', 'BU', 22);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(83.5, 69);
        $pdf->Write(0, $participant->name);

        // Jabatan & Instansi
        $jabatan  = $participant->position ?? '';
        $instansi = $participant->institution_name ?? '';
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetXY(83.5, 75);
        $pdf->MultiCell(0, 5, $jabatan . "\n" . $instansi, 0, 'L');

        // Sub Judul
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(83.5, 90);
        $pdf->MultiCell(187, 5, 'Sebagai Peserta Pelatihan/ Bimbingan Teknis', 0, 'L');

        // Tema
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(83.5, 97);
        $pdf->MultiCell(187, 5, $participant->theme ?? '', 0, 'L');

        // Blok detail: No. Sertifikat, Tanggal, Tempat, Lokasi
        $details = [
            ['No. Sertifikat', $participant->certificate_number ?? '-'],
            ['Tanggal',        $participant->certificate_date ?? '-'],
            ['Tempat',         $participant->event_venue ?? '-'],
            ['Lokasi',         $participant->event_location ?? '-'],
        ];

        $startX      = 114;
        $startY      = 119.5;
        $lineHeight  = 5;
        $labelWidth  = 27; // Lebar tetap kolom label (mm)
        $colonWidth  = 3;  // Lebar kolom titik dua (mm)

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($details as $i => [$label, $val]) {
            $y = $startY + ($i * $lineHeight);

            // Kolom Label
            $pdf->SetXY($startX, $y);
            $pdf->Cell($labelWidth, $lineHeight, $label, 0, 0, 'L');

            // Kolom Titik Dua
            $pdf->SetXY($startX + $labelWidth, $y);
            $pdf->Cell($colonWidth, $lineHeight, ':', 0, 0, 'L');

            // Kolom Nilai Teks
            $pdf->SetXY($startX + $labelWidth + $colonWidth + 1, $y);
            $pdf->Cell(120, $lineHeight, $val, 0, 0, 'L');
        }

        // Catatan: Barcode / QR Code dan teks "Scan Here" sengaja tidak dirender
        // sesuai kebutuhan Tren Media Diklat saat ini.
    }

    // =========================================================================
    // Render: Kwitansi Tren Media Diklat
    // =========================================================================

    protected function renderKwitansi(Fpdi $pdf, TrenParticipant $participant): void
    {
        $safeCert = str_replace('/', '_', $participant->receipt_number ?: ($participant->certificate_number ?? ''));
        $pdf->SetTitle('Kwitansi_' . $participant->name . ' (' . $safeCert . ')');

        // Tanggal Kwitansi (Kanan Atas)
        $receiptDate = $participant->receipt_date ?: ($participant->registration_date ?? '');
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(90, 39);
        $pdf->MultiCell(100, 7, $receiptDate, 0, 'R');

        // Nomor Kwitansi
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(90, 56);
        $pdf->MultiCell(100, 7, $participant->receipt_number ?? $participant->certificate_number, 0, 'R');

        // Nama Penerima
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(67, 79.3);
        $pdf->Write(0, $participant->name);

        // Jabatan & Instansi
        $jabatan  = $participant->position ?? '';
        $instansi = $participant->institution_name ?? '';
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(67, 82);
        $pdf->MultiCell(0, 4.5, $jabatan . "\n" . $instansi, 0, 'L');

        // Nominal Rupiah (angka)
        $amount          = (float) ($participant->amount ?? 0);
        $amountFormatted = 'Rp. ' . number_format($amount, 0, ',', '.') . ',-';
        $pdf->SetFont('Arial', 'BI', 11);
        $pdf->SetXY(68, 96);
        $pdf->MultiCell(0, 5, $amountFormatted, 0, 'L');

        // Terbilang
        $terbilang = self::terbilang((int) $amount);
        $terbilang = '(' . ucwords($terbilang) . ' Rupiah)';
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(96, 96);
        $pdf->MultiCell(0, 5, $terbilang, 0, 'L');

        // Label tema
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(67, 106.2);
        $pdf->MultiCell(100, 7, 'Pelatihan/ Bimbingan Teknis :', 0, 'L');

        // Tema
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(67, 113);
        $pdf->MultiCell(122, 5, $participant->theme ?? '', 0, 'L');

        // Pelaksanaan Kegiatan: Tanggal Pelaksanaan Bimtek, Tempat, Lokasi
        $eventDate = $participant->certificate_date ?: ($participant->receipt_date ?? '');
        $venue     = $participant->event_venue ?? '';
        $location  = $participant->event_location ?? '';
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(67, 138.3);
        $pdf->MultiCell(120, 5, $eventDate . ', ' . $venue . ' - ' . $location, 0, 'L');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function buildFilename(TrenParticipant $participant, string $type): string
    {
        $safe   = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $participant->name);
        $certNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $participant->certificate_number ?? 'nonum');

        if ($type === 'kwitansi') {
            return "Kwitansi_Tren_{$safe}_{$certNo}.pdf";
        }

        if ($type === 'blank') {
            return "Sertifikat_Blank_Tren_{$safe}_{$certNo}.pdf";
        }

        return "Sertifikat_Tren_{$safe}_{$certNo}.pdf";
    }

    /**
     * Konversi angka integer ke kata terbilang bahasa Indonesia.
     */
    public static function terbilang(int $angka): string
    {
        if ($angka < 0) {
            return 'minus ' . static::terbilang(abs($angka));
        }

        $kata = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
                 'sepuluh', 'sebelas'];

        if ($angka < 12) {
            return $kata[$angka];
        }

        if ($angka < 20) {
            return static::terbilang($angka - 10) . ' belas';
        }

        if ($angka < 100) {
            return static::terbilang((int) ($angka / 10)) . ' puluh' .
                   ($angka % 10 > 0 ? ' ' . static::terbilang($angka % 10) : '');
        }

        if ($angka < 200) {
            return 'seratus' . ($angka % 100 > 0 ? ' ' . static::terbilang($angka % 100) : '');
        }

        if ($angka < 1_000) {
            return static::terbilang((int) ($angka / 100)) . ' ratus' .
                   ($angka % 100 > 0 ? ' ' . static::terbilang($angka % 100) : '');
        }

        if ($angka < 2_000) {
            return 'seribu' . ($angka % 1_000 > 0 ? ' ' . static::terbilang($angka % 1_000) : '');
        }

        if ($angka < 1_000_000) {
            return static::terbilang((int) ($angka / 1_000)) . ' ribu' .
                   ($angka % 1_000 > 0 ? ' ' . static::terbilang($angka % 1_000) : '');
        }

        if ($angka < 1_000_000_000) {
            return static::terbilang((int) ($angka / 1_000_000)) . ' juta' .
                   ($angka % 1_000_000 > 0 ? ' ' . static::terbilang($angka % 1_000_000) : '');
        }

        if ($angka < 1_000_000_000_000) {
            return static::terbilang((int) ($angka / 1_000_000_000)) . ' miliar' .
                   ($angka % 1_000_000_000 > 0 ? ' ' . static::terbilang($angka % 1_000_000_000) : '');
        }

        return static::terbilang((int) ($angka / 1_000_000_000_000)) . ' triliun' .
               ($angka % 1_000_000_000_000 > 0 ? ' ' . static::terbilang($angka % 1_000_000_000_000) : '');
    }
}

