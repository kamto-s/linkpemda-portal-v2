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
        $templatePath = $this->resolveTemplatePath($type);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $this->registerCustomFonts($pdf);
        $pdf->setSourceFile($templatePath);
        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);
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
     * Mengambil path template PDF Tren Media Diklat berdasarkan SystemSetting dengan fallback ke aset lokal.
     * Mendukung URL eksternal (di-download & di-cache secara otomatis) maupun path lokal.
     *
     * @param  string  $type  'default' | 'blank' | 'kwitansi'
     */
    public function resolveTemplatePath(string $type = 'default'): string
    {
        // 1. Ambil setting key dari SystemSetting
        $settingKey = match ($type) {
            'blank' => 'temp_landscape_blank',
            'kwitansi' => 'temp_tren_kwitansi',
            default => 'temp_tren_sertifikat',
        };

        $configuredPath = trim((string) system_setting($settingKey, ''));

        // Fallback jika blank landscape belum diset, cek alternatif
        if ($type === 'blank' && empty($configuredPath)) {
            $configuredPath = trim((string) system_setting('temp_tren_sertifikat_blank', ''));
        }

        // 2. Jika konfigurasi di SystemSetting ada, proses path/URL-nya
        if (!empty($configuredPath)) {
            $resolved = $this->resolveFileFromConfig($configuredPath);
            if ($resolved && file_exists($resolved)) {
                return $this->getCompatibleTemplatePath($resolved);
            }
        }

        // 3. Fallback aset lokal jika setting kosong atau file belum dapat diakses
        $fallback = match ($type) {
            'blank' => file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat_blank_tren.pdf'))
                ? public_path('assets/pdf-templates/trenmedia/sertifikat_blank_tren.pdf')
                : (file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat_blank.pdf'))
                    ? public_path('assets/pdf-templates/trenmedia/sertifikat_blank.pdf')
                    : public_path('assets/pdf-templates/linkpemda/sertifikat_blank.pdf')),
            'kwitansi' => file_exists(public_path('assets/pdf-templates/trenmedia/kwitansi.pdf'))
                ? public_path('assets/pdf-templates/trenmedia/kwitansi.pdf')
                : public_path('assets/pdf-templates/linkpemda/kwitansi.pdf'),
            default => file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat_tren.pdf'))
                ? public_path('assets/pdf-templates/trenmedia/sertifikat_tren.pdf')
                : (file_exists(public_path('assets/pdf-templates/trenmedia/sertifikat.pdf'))
                    ? public_path('assets/pdf-templates/trenmedia/sertifikat.pdf')
                    : public_path('assets/pdf-templates/linkpemda/sertifikat.pdf')),
        };

        return $this->getCompatibleTemplatePath($fallback);
    }

    /**
     * Memvalidasi dan menyiapkan file fisik dari konfigurasi (bisa berupa URL HTTP/HTTPS atau path lokal).
     */
    protected function resolveFileFromConfig(string $configuredPath): ?string
    {
        // A. Jika berupa URL (http / https)
        if (str_starts_with($configuredPath, 'http://') || str_starts_with($configuredPath, 'https://')) {
            $filename = basename(parse_url($configuredPath, PHP_URL_PATH) ?: '');
            if (empty($filename)) {
                $filename = 'template_' . md5($configuredPath) . '.pdf';
            }

            // Cek 1: Apakah file ada di public/uploads lokal?
            $localUpload = public_path('uploads/' . $filename);
            if (file_exists($localUpload) && filesize($localUpload) > 0) {
                return $localUpload;
            }

            // Cek 2: Apakah file sudah pernah di-cache di storage?
            $cacheDir = storage_path('app/pdf-templates-cache');
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }

            $cachedFile = $cacheDir . '/' . md5($configuredPath) . '_' . $filename;
            if (file_exists($cachedFile) && filesize($cachedFile) > 0) {
                return $cachedFile;
            }

            // Cek 3: Download dari URL dan simpan ke cache
            try {
                $ch = curl_init($configuredPath);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_USERAGENT => 'Linkpemda-Portal/2.0',
                ]);
                $data = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($data !== false && $httpCode === 200 && strlen($data) > 0) {
                    file_put_contents($cachedFile, $data);
                    return $cachedFile;
                }
            } catch (\Throwable) {
                // Lewati dan gunakan fallback jika koneksi gagal
            }

            return null;
        }

        // B. Jika berupa path lokal absolut
        if (file_exists($configuredPath) && !is_dir($configuredPath)) {
            return $configuredPath;
        }

        // C. Jika berupa path relatif dari public_path()
        $relativePublic = public_path(ltrim($configuredPath, '/\\'));
        if (file_exists($relativePublic) && !is_dir($relativePublic)) {
            return $relativePublic;
        }

        // D. Jika berupa path relatif dari storage_path()
        $relativeStorage = storage_path('app/' . ltrim($configuredPath, '/\\'));
        if (file_exists($relativeStorage) && !is_dir($relativeStorage)) {
            return $relativeStorage;
        }

        return null;
    }

    /**
     * Stream PDF langsung ke browser (inline / tampil di tab baru).
     */
    public function streamInline(TrenParticipant $participant, string $type = 'default'): \Illuminate\Http\Response
    {
        $pdf = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);
        $content = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Stream PDF sebagai unduhan (download / save-as).
     */
    public function streamDownload(TrenParticipant $participant, string $type = 'default'): \Illuminate\Http\Response
    {
        $pdf = $this->buildPdf($participant, $type);
        $filename = $this->buildFilename($participant, $type);
        $content = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Mengembalikan raw binary string dokumen PDF.
     */
    public function getPdfBinary(TrenParticipant $participant, string $type = 'default'): string
    {
        $pdf = $this->buildPdf($participant, $type);
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
            'blank' => 'blank',
            default => 'default',
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

    /**
     * Mendaftarkan font kustom (Great Vibes & Lato) untuk dokumen Tren Media.
     */
    protected function registerCustomFonts(Fpdi $pdf): void
    {
        $fontDir = public_path('assets/fonts');

        if (file_exists($fontDir . '/GreatVibes-Regular.json')) {
            $pdf->AddFont('GreatVibes', '', 'GreatVibes-Regular.json', $fontDir);
        }

        if (file_exists($fontDir . '/Lato-Regular.json')) {
            $pdf->AddFont('Lato', '', 'Lato-Regular.json', $fontDir);
        }
        if (file_exists($fontDir . '/Lato-Bold.json')) {
            $pdf->AddFont('Lato', 'B', 'Lato-Bold.json', $fontDir);
        }
        if (file_exists($fontDir . '/Lato-Italic.json')) {
            $pdf->AddFont('Lato', 'I', 'Lato-Italic.json', $fontDir);
        }
    }

    // =========================================================================
    // Render: Sertifikat Tren Media Diklat
    // =========================================================================

    protected function renderSertifikat(Fpdi $pdf, TrenParticipant $participant): void
    {
        $safeCert = str_replace('/', '_', $participant->certificate_number ?? '');
        $pdf->SetTitle('Sertifikat_' . $participant->name . ' (' . $safeCert . ')');

        // Total lebar halaman A4 Landscape = 297 mm, Tinggi = 210 mm
        // Teks utama menggunakan perataan RATA TENGAH (align: 'C')
        $pageWidth = 297;

        // ---------------------------------------------------------------------
        // 1. NOMOR SERTIFIKAT (Tepat di bawah tulisan SERTIFIKAT)
        // ---------------------------------------------------------------------
        $pdf->SetFont('Lato', '', 12);
        $pdf->SetTextColor(120, 30, 30); // Warna merah marun
        $pdf->SetXY(0, 46);            // <--- Atur koordinat Y (vertikal) di sini
        $pdf->Cell($pageWidth, 6, 'No: ' . ($participant->certificate_number ?? '-'), 0, 0, 'C');

        // ---------------------------------------------------------------------
        // 2. TEKS "Diberikan Kepada"
        // ---------------------------------------------------------------------
        $pdf->SetFont('Lato', 'I', 12);
        $pdf->SetTextColor(35, 35, 35);
        $pdf->SetXY(0, 56);            // <--- Atur koordinat Y (vertikal) di sini
        $pdf->Cell($pageWidth, 6, 'Diberikan Kepada', 0, 0, 'C');

        // ---------------------------------------------------------------------
        // 3. NAMA PESERTA (Font: Great Vibes)
        // ---------------------------------------------------------------------
        $pdf->SetFont('GreatVibes', '', 46); // Font: Great Vibes (script font)
        $pdf->SetTextColor(15, 15, 15);
        $pdf->SetXY(0, 67);               // <--- Atur koordinat Y (vertikal) di sini
        $pdf->Cell($pageWidth, 14, $participant->name, 0, 0, 'C');

        // ---------------------------------------------------------------------
        // 4. JABATAN & INSTANSI PESERTA (Font: Lato)
        // ---------------------------------------------------------------------
        $jabatan = $participant->position ?? '';
        $instansi = $participant->institution_name ?? '';

        $pdf->SetFont('Lato', '', 12);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->SetXY(20, 85.5);              // <--- Koordinat Y Jabatan
        $pdf->Cell($pageWidth - 40, 5.5, $jabatan, 0, 0, 'C');

        $pdf->SetXY(20, 91);            // <--- Koordinat Y Instansi
        $pdf->Cell($pageWidth - 40, 5.5, $instansi, 0, 0, 'C');

        // ---------------------------------------------------------------------
        // 5. STATUS PERAN KEPESERTAAN (Font: Lato Bold)
        // ---------------------------------------------------------------------
        $pdf->SetFont('Lato', 'B', 12);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->SetXY(20, 102);             // <--- Koordinat Y Peran
        $pdf->Cell($pageWidth - 40, 6, 'Sebagai Peserta Pelatihan / Bimbingan Teknis', 0, 0, 'C');

        // ---------------------------------------------------------------------
        // 6. TEMA / JUDUL KEGIATAN (Font: Lato Italic)
        // ---------------------------------------------------------------------
        // Atur LEBAR MAKSIMAL kotak tema di sini (dalam mm):
        // Misalnya 210 mm (artinya teks dibatasi max 210 mm, tidak akan pernah melebar lebih dari ini).
        // Posisi X otomatis dihitung agar selalu persis rata tengah di antara margin kiri & kanan.
        $maxLebarTema = 210; // <-- UBAH ANGKA INI: makin kecil nilainya, makin sempit batas kanan-kirinya
        $posisiXTema = ($pageWidth - $maxLebarTema) / 2; // Otomatis rata tengah sempurna

        $pdf->SetFont('Lato', 'I', 12);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetDrawColor(220, 53, 69);  // Garis merah pembatas
        $pdf->SetXY($posisiXTema, 110);   // Posisi X otomatis di tengah & Y: 110 mm
        // Parameter ke-4: ganti 1 (ada garis kotak merah) atau 0 (tanpa garis)
        $pdf->MultiCell($maxLebarTema, 5.2, $participant->theme ?? '', 0, 'C');
        $pdf->SetDrawColor(0, 0, 0);      // Reset warna garis kembali normal

        // ---------------------------------------------------------------------
        // 7. WAKTU & TEMPAT PELAKSANAAN
        // ---------------------------------------------------------------------
        $pdf->SetFont('Lato', '', 12);
        $pdf->SetTextColor(40, 40, 40);
        $pdf->SetXY(20, 129.5);             // <--- Koordinat Y Pengantar Tanggal
        $pdf->Cell($pageWidth - 40, 5.5, 'yang dilaksanakan pada tanggal', 0, 0, 'C');

        // Format: [Tanggal] di [Tempat], [Lokasi] (Font: Lato Bold)
        $detailLokasi = array_filter([$participant->event_venue, $participant->event_location]);
        $teksTempat = !empty($detailLokasi) ? ' di ' . implode(', ', $detailLokasi) : '';
        $tglTempat = trim(($participant->certificate_date ?? '-') . $teksTempat);

        $pdf->SetFont('Lato', '', 12);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->SetXY(20, 135);           // <--- Koordinat Y Tanggal & Tempat
        $pdf->Cell($pageWidth - 40, 5.5, $tglTempat, 0, 0, 'C');
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
        $jabatan = $participant->position ?? '';
        $instansi = $participant->institution_name ?? '';
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(67, 82);
        $pdf->MultiCell(0, 4.5, $jabatan . "\n" . $instansi, 0, 'L');

        // Nominal Rupiah (angka)
        $amount = (float) ($participant->amount ?? 0);
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
        $venue = $participant->event_venue ?? '';
        $location = $participant->event_location ?? '';
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(67, 138.3);
        $pdf->MultiCell(120, 5, $eventDate . ', ' . $venue . ' - ' . $location, 0, 'L');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function buildFilename(TrenParticipant $participant, string $type): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $participant->name);
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

        $kata = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

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

    /**
     * Memastikan file template PDF kompatibel dengan parser FPDI (versi gratis).
     * FPDI free tidak mendukung kompresi /Type /XRef (cross-reference stream) ala PDF 1.5+.
     * Fungsi ini secara otomatis mendeteksi dan menormalkan kembali struktur tabel xref tradisional.
     */
    protected function getCompatibleTemplatePath(string $path): string
    {
        if (!file_exists($path) || !is_readable($path)) {
            return $path;
        }

        $content = @file_get_contents($path);
        if ($content === false || strpos($content, '/XRef') === false) {
            return $path;
        }

        if (!preg_match('/\/Type\s*\/XRef/i', $content)) {
            return $path;
        }

        return $this->repairXrefStream($path, $content);
    }

    /**
     * Membangun ulang tabel xref konvensional (PDF 1.4) dari dokumen PDF yang memiliki stream /XRef.
     */
    protected function repairXrefStream(string $path, string $content): string
    {
        try {
            $cleanPdf = preg_replace('/^%PDF-\d+\.\d+/', '%PDF-1.4', $content);

            if (preg_match('/(\d+)\s+0\s+obj\s*<<[^>]*\/Type\s*\/XRef.*?%%EOF/s', $cleanPdf, $m, PREG_OFFSET_CAPTURE)) {
                $cleanPdf = substr($cleanPdf, 0, $m[0][1]);
            }

            preg_match_all('/(\d+)\s+0\s+obj/s', $cleanPdf, $matches, PREG_OFFSET_CAPTURE);
            if (empty($matches[1])) {
                return $path;
            }

            $objects = [];
            foreach ($matches[1] as $idx => $match) {
                $num = (int) $match[0];
                $offset = $matches[0][$idx][1];
                $objects[$num] = $offset;
            }

            ksort($objects);
            $maxObj = max(array_keys($objects));

            $xrefOffset = strlen($cleanPdf);
            $xref = "xref\n0 " . ($maxObj + 1) . "\n0000000000 65535 f \n";

            for ($i = 1; $i <= $maxObj; $i++) {
                if (isset($objects[$i])) {
                    $xref .= sprintf("%010d 00000 n \n", $objects[$i]);
                } else {
                    $xref .= "0000000000 65535 f \n";
                }
            }

            $root = '1 0 R';
            $info = '';
            if (preg_match('/\/Root\s+(\d+\s+\d+\s+R)/', $cleanPdf, $rm)) {
                $root = $rm[1];
            }
            if (preg_match('/\/Info\s+(\d+\s+\d+\s+R)/', $cleanPdf, $im)) {
                $info = "\n/Info " . $im[1];
            }

            $trailer = "trailer\n<<\n/Size " . ($maxObj + 1) . "\n/Root " . $root . $info . "\n>>\n";
            $trailer .= "startxref\n" . $xrefOffset . "\n%%EOF\n";

            $finalPdf = $cleanPdf . $xref . $trailer;

            // Jika file asli writable, perbarui file aslinya
            if (is_writable($path)) {
                @file_put_contents($path, $finalPdf);
                return $path;
            }

            // Fallback ke storage cache
            $cacheDir = storage_path('app/pdf-templates-cache');
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            $cachePath = $cacheDir . '/' . md5($path . filemtime($path)) . '.pdf';
            @file_put_contents($cachePath, $finalPdf);

            return file_exists($cachePath) ? $cachePath : $path;
        } catch (\Throwable) {
            return $path;
        }
    }
}

