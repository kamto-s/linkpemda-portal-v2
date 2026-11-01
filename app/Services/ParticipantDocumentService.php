<?php

namespace App\Services;

use App\Models\Participant;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use setasign\Fpdi\Fpdi;

/**
 * Service untuk pembuatan dokumen PDF (Sertifikat & Kwitansi)
 * dan QR Code untuk peserta Linkpemda.
 */
class ParticipantDocumentService
{
    /**
     * Menghasilkan dan menyimpan file gambar QR Code ke public/uploads/qr_code/.
     * Link QR Code mengarah ke halaman verifikasi sertifikat publik.
     *
     * @return string|false  Nama file PNG yang tersimpan, atau false jika gagal.
     */
    public function generateAndSaveQrCode(Participant $participant): string|false
    {
        $certNumber = $participant->certificate_number;

        if (empty($certNumber)) {
            return false;
        }

        // URL verifikasi publik yang di-encode ke QR
        $verifyUrl = route('certificate.verify', ['certificate_number' => $certNumber]);

        $options = new QROptions([
            'outputInterface'  => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'eccLevel'         => \chillerlan\QRCode\Common\EccLevel::L,
            'outputBase64'     => false,
            'imageTransparent' => false,
            'scale'            => 10,
        ]);

        $qr = new QRCode($options);
        $imageData = $qr->render($verifyUrl);

        $dir = public_path('uploads/qr_code');
        if (! file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $certNumber);
        $filename  = "{$sanitized}.png";
        $filePath  = "{$dir}/{$filename}";

        if (file_put_contents($filePath, $imageData) === false) {
            return false;
        }

        return $filename;
    }

    /**
     * Menyiapkan instance FPDI berdasarkan template & tipe dokumen, lalu merender
     * teks dan gambar peserta di atas template PDF.
     *
     * @param  string  $type  'default' = sertifikat berwarna | 'blank' = sertifikat blank | 'kwitansi'
     */
    public function buildPdf(Participant $participant, string $type = 'default'): Fpdi
    {
        $templatePath = match ($type) {
            'blank'    => public_path('assets/pdf-templates/sertifikat_blank.pdf'),
            'kwitansi' => public_path('assets/pdf-templates/kwitansi.pdf'),
            default    => public_path('assets/pdf-templates/sertifikat.pdf'),
        };

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
    public function streamInline(Participant $participant, string $type = 'default'): \Illuminate\Http\Response
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
    public function streamDownload(Participant $participant, string $type = 'default'): \Illuminate\Http\Response
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
     * Mengembalikan raw binary string dokumen PDF (cocok untuk attachment email / zip).
     */
    public function getPdfBinary(Participant $participant, string $type = 'default'): string
    {
        $pdf      = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);

        return $pdf->Output($filename, 'S');
    }

    /**
     * Membuat file ZIP di direktori temporary yang berisi kumpulan file PDF peserta.
     * Mengembalikan path ke file ZIP temporary tersebut.
     */
    public function buildBulkZip(iterable $participants, string $type = 'sertifikat'): ?string
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'lp_docs_') . '.zip';
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
    // Render: Sertifikat
    // =========================================================================

    protected function renderSertifikat(Fpdi $pdf, Participant $participant): void
    {
        $pdf->SetTitle('Sertifikat_' . $participant->name . ' (' . $participant->certificate_number . ')');

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
        $detail = "No. Sertifikat   : " . ($participant->certificate_number ?? '-')
                . "\nTanggal          : " . ($participant->certificate_date ?? '-')
                . "\nTempat           : " . ($participant->event_venue ?? '-')
                . "\nLokasi             : " . ($participant->event_location ?? '-');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(114, 119.5);
        $pdf->MultiCell(120, 5, $detail, 0, 'L');

        // QR Code
        $qrPath = $this->resolveQrPath($participant);
        if ($qrPath) {
            $pdf->Image($qrPath, 83.5, 115, 28, 28);
        }

        // Teks "Scan Here"
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(88, 143);
        $pdf->MultiCell(187, 5, 'Scan Here', 0, 'L');
    }

    // =========================================================================
    // Render: Kwitansi
    // =========================================================================

    protected function renderKwitansi(Fpdi $pdf, Participant $participant): void
    {
        $pdf->SetTitle('Kwitansi_' . $participant->name . ' (' . $participant->certificate_number . ')');

        // Tanggal Registrasi
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(90, 39);
        $pdf->MultiCell(100, 7, $participant->registration_date ?? '', 0, 'R');

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
        $amount        = (float) ($participant->amount ?? 0);
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

        // Tanggal kegiatan, Tempat, Lokasi
        $receiptDate = $participant->receipt_date ?? '';
        $venue       = $participant->event_venue ?? '';
        $location    = $participant->event_location ?? '';
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(67, 138.3);
        $pdf->MultiCell(120, 5, $receiptDate . ', ' . $venue . ' - ' . $location, 0, 'L');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function resolveQrPath(Participant $participant): ?string
    {
        if (empty($participant->qr_code_path)) {
            return null;
        }

        $paths = [
            public_path('uploads/qr_code/' . $participant->qr_code_path),
            public_path('assets/images/qr_codes/' . $participant->qr_code_path),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    protected function buildFilename(Participant $participant, string $type): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $participant->name);
        $certNo = $participant->certificate_number ?? 'nonum';

        if ($type === 'kwitansi') {
            return "Kwitansi_{$safe}_{$certNo}.pdf";
        }

        if ($type === 'blank') {
            return "Sertifikat_Blank_{$safe}_{$certNo}.pdf";
        }

        return "Sertifikat_{$safe}_{$certNo}.pdf";
    }

    /**
     * Konversi angka integer ke kata terbilang bahasa Indonesia.
     * Tidak butuh dependensi eksternal.
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

