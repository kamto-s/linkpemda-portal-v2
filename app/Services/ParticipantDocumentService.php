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
            'absensi' => 'temp_absensi_linkpemda',
            default => 'temp_linkpemda_sertifikat',
        };

        $configuredPath = trim((string) system_setting($settingKey, ''));

        // Fallback jika blank landscape belum diset, cek kemungkinan key alternatif
        if ($type === 'blank' && empty($configuredPath)) {
            $configuredPath = trim((string) system_setting('temp_linkpemda_sertifikat_blank', ''));
        }

        // Fallback jika absensi belum diset di db, fallback ke template kop surat kwitansi/invoice
        if ($type === 'absensi' && empty($configuredPath)) {
            $configuredPath = trim((string) system_setting('temp_linkpemda_kwitansi', ''));
            if (empty($configuredPath)) {
                $configuredPath = trim((string) system_setting('temp_linkpemda_invoice', ''));
            }
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
            'kwitansi', 'absensi' => public_path('assets/pdf-templates/linkpemda/kwitansi.pdf'),
            default => public_path('assets/pdf-templates/linkpemda/sertifikat.pdf'),
        };

        if (!file_exists($fallback)) {
            $fallbackAlternative = match ($type) {
                'blank' => public_path('assets/pdf-templates/sertifikat_blank.pdf'),
                'kwitansi', 'absensi' => public_path('assets/pdf-templates/kwitansi.pdf'),
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

    // =========================================================================
    // Render: Absensi / Daftar Hadir (Kop Surat A4 Portrait)
    // =========================================================================

    /**
     * Menghasilkan berkas PDF Absensi / Daftar Hadir resmi Linkpemda untuk kumpulan peserta.
     * Menggunakan template kop surat dari setting temp_absensi_linkpemda.
     *
     * @param iterable<Participant> $participants
     * @return string Binary string PDF
     */
    public function generateAbsensiPdf(iterable $participants): string
    {
        $list = collect($participants)->values();
        if ($list->isEmpty()) {
            throw new \InvalidArgumentException('Daftar peserta untuk absensi tidak boleh kosong.');
        }

        $templatePath = $this->resolveTemplatePath('absensi');

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->setSourceFile($templatePath);
        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);

        $addNewPage = function () use ($pdf, $template, $size) {
            $pdf->AddPage($size['orientation'] ?? 'P', [$size['width'] ?? 210, $size['height'] ?? 297]);
            $pdf->useTemplate($template, 0, 0, $size['width'] ?? 210, $size['height'] ?? 297);
        };

        // Ambil data event dari peserta pertama / data umum
        $firstP = $list->first();
        $dateStr = trim((string) ($firstP->certificate_date ?: ($firstP->registration_date ?? '')));
        $sched = $this->parseAbsensiSchedule($dateStr);
        $hariText = $sched['hari'];
        $tanggalText = $sched['tanggal'];
        $daysCount = $sched['days_count'];

        // Lokasi kegiatan
        $venue = trim((string) ($firstP->event_venue ?? ''));
        $location = trim((string) ($firstP->event_location ?? ''));
        if (!empty($venue) && !empty($location)) {
            $lokasiText = $venue . ' - ' . $location;
        } elseif (!empty($venue)) {
            $lokasiText = $venue;
        } elseif (!empty($location)) {
            $lokasiText = $location;
        } else {
            $lokasiText = '-';
        }

        // Kegiatan / Tema
        $kegiatanText = trim((string) ($firstP->theme ?? '')) ?: '-';

        $pdf->SetTitle('Absensi_' . ($firstP->theme ? \Illuminate\Support\Str::slug(\Illuminate\Support\Str::limit($firstP->theme, 25), '_') : 'Linkpemda'));
        $pdf->SetTextColor(0, 0, 0);

        // Bangun daftar lembar absensi:
        // 1. Lembar Registrasi (berdiri sendiri)
        // 2. Lembar per Hari Kegiatan (Hari 1 s.d. Hari N, masing-masing 1 hari per lembar)
        $sections = [];

        $firstDayDetail = $sched['days_detail'][1] ?? null;
        $firstDateBrief = !empty($firstDayDetail['tanggal_singkat']) ? $firstDayDetail['tanggal_singkat'] : (!empty($firstDayDetail['tanggal']) ? $firstDayDetail['tanggal'] : '');

        $sections[] = [
            'type' => 'registrasi',
            'title' => 'ABSENSI REGISTRASI',
            'column_label' => 'Registrasi' . (!empty($firstDateBrief) ? ' (' . $firstDateBrief . ')' : ''),
            'day_number' => null,
            'hari' => $hariText,
            'tanggal' => $tanggalText,
        ];

        for ($d = 1; $d <= $daysCount; $d++) {
            $dayDetail = $sched['days_detail'][$d] ?? null;
            $dayDateBrief = !empty($dayDetail['tanggal_singkat']) ? $dayDetail['tanggal_singkat'] : (!empty($dayDetail['tanggal']) ? $dayDetail['tanggal'] : '');

            $sections[] = [
                'type' => 'hari',
                'title' => 'ABSENSI - HARI ' . $d,
                'column_label' => 'Hari ' . $d . (!empty($dayDateBrief) ? ' (' . $dayDateBrief . ')' : ''),
                'day_number' => $d,
                'hari' => $hariText,
                'tanggal' => $tanggalText,
            ];
        }

        // Dimensi kolom tabel absensi per 1 hari (total lebar pas 180 mm di kertas A4)
        $noW = 10.0;
        $namaW = 68.0;
        $instansiW = 57.0;
        $parafW = 45.0;
        $parafStartX = 15.0 + $noW + $namaW + $instansiW; // 150.0

        // Render setiap bagian lembar absensi
        foreach ($sections as $section) {
            // Setiap lembar/bagian selalu mulai di halaman baru lengkap dengan Kop Surat
            $addNewPage();

            // 1. JUDUL DOKUMEN
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetXY(15, 50);
            $pdf->Cell(180, 7, $section['title'], 0, 1, 'C');

            // 2. METADATA (Hari, Tanggal, Lokasi, Kegiatan)
            $metaY = 60;
            $labelW = 22;
            $colonX = 37;
            $valX = 40;
            $valW = 155;
            $lineH = 5.2;

            $metaItems = [
                ['Hari', $section['hari']],
                ['Tanggal', $section['tanggal']],
                ['Lokasi', $lokasiText],
                ['Kegiatan', $kegiatanText],
            ];

            foreach ($metaItems as [$lbl, $val]) {
                $pdf->SetFont('Arial', 'B', 9.5);
                $pdf->SetXY(15, $metaY);
                $pdf->Cell($labelW, $lineH, $lbl, 0, 0, 'L');
                $pdf->SetXY($colonX, $metaY);
                $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

                $pdf->SetFont('Arial', '', 9.5);
                $pdf->SetXY($valX, $metaY);
                if ($lbl === 'Kegiatan' && $pdf->GetStringWidth($val) > $valW) {
                    $pdf->MultiCell($valW, 4.6, $val, 0, 'L');
                    $metaY = $pdf->GetY() + 1.0;
                } else {
                    $pdf->Cell($valW, $lineH, $val, 0, 1, 'L');
                    $metaY += $lineH;
                }
            }

            // Callback render Header Tabel
            $columnLabel = $section['column_label'];
            $renderHeader = function (float $startY) use ($pdf, $noW, $namaW, $instansiW, $parafW, $parafStartX, $columnLabel) {
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.3);

                // Baris 1: Kolom No (12 mm)
                $pdf->Rect(15, $startY, $noW, 12);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY(15, $startY + 3.5);
                $pdf->Cell($noW, 5, 'No', 0, 0, 'C');

                // Baris 1: Kolom Nama (12 mm)
                $pdf->Rect(15 + $noW, $startY, $namaW, 12);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY(15 + $noW, $startY + 3.5);
                $pdf->Cell($namaW, 5, 'Nama', 0, 0, 'C');

                // Baris 1: Kolom Instansi (12 mm)
                $instX = 15 + $noW + $namaW;
                $pdf->Rect($instX, $startY, $instansiW, 12);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY($instX, $startY + 3.5);
                $pdf->Cell($instansiW, 5, 'Instansi', 0, 0, 'C');

                // Baris 1: Kolom Utama Paraf (6 mm)
                $pdf->Rect($parafStartX, $startY, $parafW, 6);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY($parafStartX, $startY + 0.5);
                $pdf->Cell($parafW, 5, 'Paraf', 0, 0, 'C');

                // Baris 2: Sub-kolom Nama Hari / Registrasi (6 mm)
                $pdf->Rect($parafStartX, $startY + 6, $parafW, 6);
                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->SetXY($parafStartX, $startY + 6.5);
                $pdf->Cell($parafW, 5, $columnLabel, 0, 0, 'C');
            };

            // Render Header di Halaman Awal Lembar Ini
            $tableStartY = $metaY + 4;
            $renderHeader($tableStartY);

            // DATA BARIS PESERTA
            $curY = $tableStartY + 12;
            $maxPageY = 275;

            foreach ($list as $idx => $p) {
                $rawName = trim((string) ($p->name ?? '')) ?: '-';
                $rawInstansi = trim((string) ($p->institution_name ?? '')) ?: '-';

                $nameFontSize = 8.5;
                $instFontSize = 8.0;
                $pdf->SetFont('Arial', '', $nameFontSize);
                $nameLines = $this->wrapTextToWidth($pdf, $rawName, $namaW - 3);
                $pdf->SetFont('Arial', '', $instFontSize);
                $instLines = $this->wrapTextToWidth($pdf, $rawInstansi, $instansiW - 3);

                $lineCount = max(count($nameLines), count($instLines), 1);
                $lineSpacing = 3.8;
                $minRowH = 9.0;
                $actualRowH = max($minRowH, ($lineCount * $lineSpacing) + 2.8);

                if ($curY + $actualRowH > $maxPageY) {
                    $addNewPage();
                    $tableStartY = 50;
                    $renderHeader($tableStartY);
                    $curY = $tableStartY + 12;
                }

                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2);

                // Kolom No
                $pdf->Rect(15, $curY, $noW, $actualRowH);
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY(15, $curY + ($actualRowH / 2) - 2.5);
                $pdf->Cell($noW, 5, (string) ($idx + 1), 0, 0, 'C');

                // Kolom Nama (multi-line)
                $pdf->Rect(15 + $noW, $curY, $namaW, $actualRowH);
                $nameBlockH = count($nameLines) * $lineSpacing;
                $nameStartY = $curY + ($actualRowH - $nameBlockH) / 2;
                $pdf->SetFont('Arial', '', $nameFontSize);
                foreach ($nameLines as $k => $l) {
                    $pdf->SetXY(15 + $noW + 1.5, $nameStartY + ($k * $lineSpacing));
                    $pdf->Cell($namaW - 3, $lineSpacing, $l, 0, 0, 'L');
                }

                // Kolom Instansi (multi-line)
                $instX = 15 + $noW + $namaW;
                $pdf->Rect($instX, $curY, $instansiW, $actualRowH);
                $instBlockH = count($instLines) * $lineSpacing;
                $instStartY = $curY + ($actualRowH - $instBlockH) / 2;
                $pdf->SetFont('Arial', '', $instFontSize);
                foreach ($instLines as $k => $l) {
                    $pdf->SetXY($instX + 1.5, $instStartY + ($k * $lineSpacing));
                    $pdf->Cell($instansiW - 3, $lineSpacing, $l, 0, 0, 'L');
                }

                // Kolom Paraf (kotak tanda tangan bersih selebar 45 mm)
                $pdf->Rect($parafStartX, $curY, $parafW, $actualRowH);

                $curY += $actualRowH;
            }
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Mem-parsing string tanggal kegiatan untuk mengekstrak hari, tanggal rapi, dan jumlah hari acara.
     */
    public function parseAbsensiSchedule(string $dateStr): array
    {
        $dateStr = trim($dateStr);
        if (empty($dateStr)) {
            return [
                'hari' => '-',
                'tanggal' => '-',
                'days_count' => 2,
            ];
        }

        $monthMap = [
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'jun' => 6,
            'jul' => 7,
            'agu' => 8,
            'agt' => 8,
            'sep' => 9,
            'okt' => 10,
            'nov' => 11,
            'des' => 12,
        ];

        // Format 1: "11 - 12 November 2026" atau "07 s.d 10 November 2026"
        if (preg_match('~^(\d{1,2})\s*(?:[-–—]|s\.d\.?|s/d)\s*(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$~iu', $dateStr, $m)) {
            $startDay = (int) $m[1];
            $endDay = (int) $m[2];
            $monthName = strtolower($m[3]);
            $year = (int) $m[4];

            if (isset($monthMap[$monthName])) {
                try {
                    $start = \Carbon\Carbon::createFromDate($year, $monthMap[$monthName], $startDay)->locale('id');
                    $end = \Carbon\Carbon::createFromDate($year, $monthMap[$monthName], $endDay)->locale('id');

                    $hariStart = $start->translatedFormat('l');
                    $hariEnd = $end->translatedFormat('l');
                    $hariText = ($startDay === $endDay) ? $hariStart : ($hariStart . ' - ' . $hariEnd);
                    $daysCount = max(1, abs($endDay - $startDay) + 1);

                    $daysDetail = [];
                    for ($d = 1; $d <= $daysCount; $d++) {
                        $cur = (clone $start)->addDays($d - 1);
                        $daysDetail[$d] = [
                            'hari' => $cur->translatedFormat('l'),
                            'tanggal' => $cur->translatedFormat('d F Y'),
                            'tanggal_singkat' => $cur->translatedFormat('d M Y'),
                        ];
                    }

                    return [
                        'hari' => $hariText,
                        'tanggal' => $dateStr,
                        'days_count' => $daysCount,
                        'days_detail' => $daysDetail,
                    ];
                } catch (\Throwable) {
                }
            }
        }

        // Format 2: Tanggal tunggal "15 November 2026"
        if (preg_match('~^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$~iu', $dateStr, $m)) {
            $day = (int) $m[1];
            $monthName = strtolower($m[2]);
            $year = (int) $m[3];

            if (isset($monthMap[$monthName])) {
                try {
                    $date = \Carbon\Carbon::createFromDate($year, $monthMap[$monthName], $day)->locale('id');

                    return [
                        'hari' => $date->translatedFormat('l'),
                        'tanggal' => $dateStr,
                        'days_count' => 1,
                        'days_detail' => [
                            1 => [
                                'hari' => $date->translatedFormat('l'),
                                'tanggal' => $date->translatedFormat('d F Y'),
                                'tanggal_singkat' => $date->translatedFormat('d M Y'),
                            ],
                        ],
                    ];
                } catch (\Throwable) {
                }
            }
        }

        // Format 3: Lintas bulan "29 November - 01 Desember 2026" atau "29 Nov 2026 s.d 01 Des 2026"
        if (preg_match('~^(\d{1,2})\s+([a-zA-Z]+)(?:\s+(\d{4}))?\s*(?:[-–—]|s\.d\.?|s/d)\s*(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$~iu', $dateStr, $m)) {
            $startDay = (int) $m[1];
            $m1 = strtolower($m[2]);
            $endDay = (int) $m[4];
            $m2 = strtolower($m[5]);
            $year = (int) $m[6];
            $y1 = !empty($m[3]) ? (int) $m[3] : $year;

            if (isset($monthMap[$m1], $monthMap[$m2])) {
                try {
                    $start = \Carbon\Carbon::createFromDate($y1, $monthMap[$m1], $startDay)->locale('id');
                    $end = \Carbon\Carbon::createFromDate($year, $monthMap[$m2], $endDay)->locale('id');

                    $hariStart = $start->translatedFormat('l');
                    $hariEnd = $end->translatedFormat('l');
                    $daysCount = max(1, (int) $start->diffInDays($end, true) + 1);

                    $daysDetail = [];
                    for ($d = 1; $d <= $daysCount; $d++) {
                        $cur = (clone $start)->addDays($d - 1);
                        $daysDetail[$d] = [
                            'hari' => $cur->translatedFormat('l'),
                            'tanggal' => $cur->translatedFormat('d F Y'),
                            'tanggal_singkat' => $cur->translatedFormat('d M Y'),
                        ];
                    }

                    return [
                        'hari' => $hariStart . ' - ' . $hariEnd,
                        'tanggal' => $dateStr,
                        'days_count' => $daysCount,
                        'days_detail' => $daysDetail,
                    ];
                } catch (\Throwable) {
                }
            }
        }

        return [
            'hari' => '-',
            'tanggal' => $dateStr,
            'days_count' => 2,
            'days_detail' => [
                1 => ['hari' => '-', 'tanggal' => $dateStr, 'tanggal_singkat' => $dateStr],
                2 => ['hari' => '-', 'tanggal' => $dateStr, 'tanggal_singkat' => $dateStr],
            ],
        ];
    }

    /**
     * Menyesuaikan ukuran font teks agar tidak keluar dari lebar maksimal kolom (mm).
     * Jika tetap melebihi batas setelah ukuran terkecil, teks dipangkas dengan ellipsis (...).
     */
    protected function fitText(Fpdi $pdf, string $text, float $maxW, float $baseFontSize = 9.0, float $minFontSize = 7.0): array
    {
        $pdf->SetFontSize($baseFontSize);
        if ($pdf->GetStringWidth($text) <= $maxW) {
            return [$text, $baseFontSize];
        }

        for ($sz = $baseFontSize - 0.5; $sz >= $minFontSize; $sz -= 0.5) {
            $pdf->SetFontSize($sz);
            if ($pdf->GetStringWidth($text) <= $maxW) {
                return [$text, $sz];
            }
        }

        $pdf->SetFontSize($minFontSize);
        $truncated = $text;
        while (mb_strlen($truncated) > 1 && $pdf->GetStringWidth($truncated . '...') > $maxW) {
            $truncated = mb_substr($truncated, 0, -1);
        }

        return [$truncated . '...', $minFontSize];
    }
}

