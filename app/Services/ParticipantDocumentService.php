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
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::L,
            'outputBase64' => false,
            'imageTransparent' => false,
            'scale' => 10,
        ]);

        $qr = new QRCode($options);
        $imageData = $qr->render($verifyUrl);

        $dir = public_path('uploads/qr_code');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $certNumber);
        $filename = "{$sanitized}.png";
        $filePath = "{$dir}/{$filename}";

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
        $templatePath = $this->resolveTemplatePath($type);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
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
     * Mengambil path template PDF berdasarkan SystemSetting dengan fallback ke aset lokal.
     * Mendukung URL eksternal (di-download & di-cache secara otomatis) maupun path lokal.
     *
     * @param  string  $type  'default' | 'blank' | 'kwitansi'
     */
    public function resolveTemplatePath(string $type = 'default'): string
    {
        // 1. Ambil setting key dari SystemSetting
        $settingKey = match ($type) {
            'blank' => 'temp_landscape_blank',
            'kwitansi' => 'temp_linkpemda_kwitansi',
            default => 'temp_linkpemda_sertifikat',
        };

        $configuredPath = trim((string) system_setting($settingKey, ''));

        // Fallback jika blank landscape belum diset, cek kemungkinan key alternatif
        if ($type === 'blank' && empty($configuredPath)) {
            $configuredPath = trim((string) system_setting('temp_linkpemda_sertifikat_blank', ''));
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
            'blank' => public_path('assets/pdf-templates/linkpemda/sertifikat_blank.pdf'),
            'kwitansi' => public_path('assets/pdf-templates/linkpemda/kwitansi.pdf'),
            default => public_path('assets/pdf-templates/linkpemda/sertifikat.pdf'),
        };

        if (!file_exists($fallback)) {
            $fallbackAlternative = match ($type) {
                'blank' => public_path('assets/pdf-templates/sertifikat_blank.pdf'),
                'kwitansi' => public_path('assets/pdf-templates/kwitansi.pdf'),
                default => public_path('assets/pdf-templates/sertifikat.pdf'),
            };
            if (file_exists($fallbackAlternative)) {
                $fallback = $fallbackAlternative;
            }
        }

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

        // C. Jika berupa path relatif dari public_path() (misal: 'uploads/file.pdf' atau 'assets/...')
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
    public function streamInline(Participant $participant, string $type = 'default'): \Illuminate\Http\Response
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
    public function streamDownload(Participant $participant, string $type = 'default'): \Illuminate\Http\Response
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
     * Mengembalikan raw binary string dokumen PDF (cocok untuk attachment email / zip).
     */
    public function getPdfBinary(Participant $participant, string $type = 'default'): string
    {
        $pdf = $this->buildPdf($participant, $type);
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
        $jabatan = $participant->position ?? '';
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
        // Menggunakan kolom terpisah agar tanda titik dua (:) 100% lurus sejajar presisi
        $details = [
            ['No. Sertifikat', $participant->certificate_number ?? '-'],
            ['Tanggal', $participant->certificate_date ?? '-'],
            ['Tempat', $participant->event_venue ?? '-'],
            ['Lokasi', $participant->event_location ?? '-'],
        ];

        $startX = 114;
        $startY = 119.5;
        $lineHeight = 5;
        $labelWidth = 27; // Lebar tetap kolom label (mm)
        $colonWidth = 3;  // Lebar kolom titik dua (mm)

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($details as $i => [$label, $val]) {
            $y = $startY + ($i * $lineHeight);

            // Kolom Label
            $pdf->SetXY($startX, $y);
            $pdf->Cell($labelWidth, $lineHeight, $label, 0, 0, 'L');

            // Kolom Titik Dua (posisi X absolut sama persis untuk semua baris)
            $pdf->SetXY($startX + $labelWidth, $y);
            $pdf->Cell($colonWidth, $lineHeight, ':', 0, 0, 'L');

            // Kolom Nilai Teks
            $pdf->SetXY($startX + $labelWidth + $colonWidth + 1, $y);
            $pdf->Cell(120, $lineHeight, $val, 0, 0, 'L');
        }

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
    // Render: Kwitansi Linkpemda
    // =========================================================================

    protected function renderKwitansi(Fpdi $pdf, Participant $participant): void
    {
        $safeCert = str_replace('/', '_', $participant->receipt_number ?: ($participant->certificate_number ?? ''));
        $pdf->SetTitle('Kwitansi_' . $participant->name . ' (' . $safeCert . ')');
        $pdf->SetTextColor(0, 0, 0);

        // 1. JUDUL KWITANSI & NOMOR (Pola & ukuran font sama persis dengan Invoice: Arial B 17 & Arial B 10)
        $pdf->SetFont('Arial', 'B', 17);
        $pdf->SetXY(15, 52);
        $pdf->Cell(180, 7, 'KWITANSI', 0, 1, 'C');

        $receiptNum = trim($participant->receipt_number ?: ($participant->certificate_number ?? '-'));
        $receiptNumFormatted = str_starts_with(strtoupper($receiptNum), 'NO') ? $receiptNum : ('No. ' . $receiptNum);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(15, 59);
        $pdf->Cell(180, 5, $receiptNumFormatted, 0, 1, 'C');

        // 2. TANGGAL (Kanan - Posisi X=120, Y=67 sama persis dengan tanggal Invoice)
        $rawDate = trim((string) ($participant->receipt_date ?: ($participant->registration_date ?? '')));
        if (empty($rawDate)) {
            $rawDate = now()->locale('id')->translatedFormat('d F Y');
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $rawDate)) {
            try {
                $rawDate = \Carbon\Carbon::parse($rawDate)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable) {
            }
        }
        $dateFormatted = $rawDate;

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(120, 67);
        $pdf->Cell(75, 5, $dateFormatted, 0, 1, 'R');

        // 3. BARIS FORM IDENTITAS & PEMBAYARAN (Margin X=15, Lebar label 48, Kolom nilai X=67 sama persis Invoice)
        $startY = 80;
        $lineH = 4.8;
        $labelW = 48;
        $colonX = 63;
        $valX = 67;
        $valW = 128;
        $curY = $startY;

        // Row 1: Nama Peserta
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Sudah terima dari ', 0, 0, 'L');
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->Cell($valW, $lineH, $participant->name ?: '-', 0, 1, 'L');
        $curY += $lineH + 2.2;

        // Row 2: Jabatan
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Jabatan', 0, 0, 'L');
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->Cell($valW, $lineH, $participant->position ?: '-', 0, 1, 'L');
        $curY += $lineH + 2.2;

        // Row 3: Instansi / Perusahaan
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Instansi / Perusahaan', 0, 0, 'L');
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->MultiCell($valW, 4.4, $participant->institution_name ?: '-', 0, 'L');
        $curY = max($curY + $lineH + 2.2, $pdf->GetY() + 1.8);

        // Row 4: Uang sebesar (HIGHLIGHT BOX HIJAU LEMBUT DINAMIS PANJANG & LEBAR)
        $amount = (float) ($participant->amount ?? 0);
        $amountFormatted = 'Rp. ' . number_format($amount, 0, ',', '.') . ',-';
        $terbilangWords = trim(self::terbilang((int) $amount));
        $terbilangFormatted = '(' . ($terbilangWords ? ucwords($terbilangWords) : 'Nol') . ' Rupiah)';

        // Font nominal & terbilang
        $pdf->SetFont('Arial', 'B', 9.5);
        $amountW = $pdf->GetStringWidth($amountFormatted);

        $pdf->SetFont('Arial', 'I', 9.5);
        $maxTextWidth = 120;
        $terbilangLines = $this->wrapTextToWidth($pdf, $terbilangFormatted, $maxTextWidth);

        $maxInnerW = $amountW;
        foreach ($terbilangLines as $tl) {
            $lw = $pdf->GetStringWidth($tl);
            if ($lw > $maxInnerW) {
                $maxInnerW = $lw;
            }
        }

        $boxStartX = 61;
        $valOffset = $valX - $boxStartX; // 6 mm
        $rightPadding = 8;
        $boxWidth = min(134, $valOffset + $maxInnerW + $rightPadding);
        $boxWidth = max(50, $boxWidth);

        $boxLineH = 4.4;
        $boxHeight = 3.0 + 4.8 + (count($terbilangLines) * $boxLineH);
        $boxStartY = $curY - 1.2;

        // Background box hijau muda / mint lembut dinamis mengikuti panjang & lebar teks
        $pdf->SetFillColor(235, 244, 238);
        $pdf->Rect($boxStartX, $boxStartY, $boxWidth, $boxHeight, 'F');

        // Label
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Uang sebesar', 0, 0, 'L');

        // Titik dua
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

        // Nominal angka
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->Cell($valW, $lineH, $amountFormatted, 0, 1, 'L');

        // Terbilang baris per baris (otomatis turun ke bawah)
        $pdf->SetFont('Arial', 'I', 9.5);
        $textY = $curY + 5.2;
        foreach ($terbilangLines as $tl) {
            $pdf->SetXY($valX, $textY);
            $pdf->Cell($valW, $boxLineH, $tl, 0, 1, 'L');
            $textY += $boxLineH;
        }

        $curY = $boxStartY + $boxHeight + 3.5;

        // Row 5: Untuk pembayaran
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Untuk pembayaran', 0, 0, 'L');
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

        // "Bimbingan Teknis / Pelatihan" (Bold 9.5 persis seperti di Invoice)
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->Cell($valW, $lineH, 'Bimbingan Teknis / Pelatihan', 0, 1, 'L');

        // Sub-kegiatan / tema kegiatan (Regular 9.5 persis seperti di Invoice)
        $theme = trim((string) ($participant->theme ?? ''));
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY + $lineH - 0.5);
        $pdf->MultiCell($valW, 4.4, $theme ?: '-', 0, 'L');
        $curY = $pdf->GetY() + 2.5;

        // Row 6: Pelaksanaan kegiatan
        $eventDate = trim((string) ($participant->certificate_date ?: ($participant->receipt_date ?? '')));
        $venue = trim((string) ($participant->event_venue ?? ''));
        $location = trim((string) ($participant->event_location ?? ''));
        $venueLocation = implode(', ', array_filter([$venue, $location]));

        if (!empty($eventDate) && !empty($venueLocation)) {
            $pelaksanaanText = $eventDate . ' di ' . $venueLocation;
        } elseif (!empty($eventDate)) {
            $pelaksanaanText = $eventDate;
        } elseif (!empty($venueLocation)) {
            $pelaksanaanText = $venueLocation;
        } else {
            $pelaksanaanText = '-';
        }

        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($labelW, $lineH, 'Pelaksanaan kegiatan', 0, 0, 'L');
        $pdf->SetXY($colonX, $curY);
        $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY);
        $pdf->MultiCell($valW, 4.4, $pelaksanaanText, 0, 'L');

        // 4. CATATAN & TANDA TANGAN (Format, Font & Pola persis seperti di Invoice)
        $bottomStartY = $pdf->GetY() + 14;

        // Catatan (Kiri)
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $bottomStartY);
        $pdf->Cell(80, 5, 'Catatan :', 0, 1, 'L');

        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetXY(15, $bottomStartY + 5.5);
        $pdf->MultiCell(80, 4.4, "Kwitansi ini adalah bukti pembayaran\nyang sah apabila dibubuhi tanda\ntangan oleh Bendahara serta stempel.", 0, 'L');

        // Tanda Tangan (Kanan - Mengikuti Invoice: X=135, W=60, Font Arial B 9.5, Garis di Atas Bendahara Pelaksana)
        $ttdY = $bottomStartY;
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(135, $ttdY);
        $pdf->Cell(60, 5, 'Hormat Kami', 0, 1, 'C');

        $bendaharaY = $ttdY + 24;
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(140, $bendaharaY, 190, $bendaharaY);

        $pdf->SetXY(135, $bendaharaY + 1.2);
        $pdf->Cell(60, 5, 'Bendahara Pelaksana', 0, 1, 'C');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Memotong teks menjadi beberapa baris berdasarkan lebar maksimal kolom (mm).
     * Mencegah teks keluar batas sel atau menabrak margin.
     */
    protected function wrapTextToWidth(Fpdi $pdf, string $text, float $maxWidth): array
    {
        $words = preg_split('/\s+/', trim($text));
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine === '' ? $word : ($currentLine . ' ' . $word);
            if ($pdf->GetStringWidth($testLine) <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine !== '') {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines;
    }

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

