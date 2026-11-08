<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

/**
 * Custom FPDI class dengan footer otomatis di setiap halaman.
 */
class InvoiceFpdi extends Fpdi
{
    public string $footerLeft = '';

    public function Footer(): void
    {
        // Garis pemisah tipis abu-abu di atas footer
        $this->SetDrawColor(210, 215, 225);
        $this->SetLineWidth(0.2);
        $this->Line(15, 282, 195, 282);

        // Pengaturan font & warna teks footer (slate blue / abu-abu elegan)
        $this->SetY(-15);
        $this->SetFont('Arial', '', 8.5);
        $this->SetTextColor(92, 118, 141);

        // Kiri: Linkpemda | Nomor Invoice
        $this->SetX(15);
        $this->Cell(130, 6, $this->footerLeft, 0, 0, 'L');

        // Kanan: Halaman X dari Y (Bahasa Indonesia)
        $this->SetX(145);
        $this->Cell(50, 6, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'R');
    }
}

class InvoiceDocumentService
{
    protected ?string $cachedTemplatePath = null;
    protected $importedTemplate = null;
    protected array $importedSize = [];

    /**
     * Menyiapkan instance FPDI dan menggambar seluruh elemen Invoice di atas template A4 Portrait.
     * Mendukung multi-halaman otomatis dengan template kop surat & watermark di setiap halaman.
     */
    public function buildPdf(Invoice $invoice): Fpdi
    {
        $pdf = new InvoiceFpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AliasNbPages('{nb}');

        // Siapkan teks footer kiri: Selalu diawali "Linkpemda" (penerbit invoice resmi)
        $issuerName = trim((string) system_setting('app_name', 'Linkpemda')) ?: 'Linkpemda';
        $docNum = $invoice->invoice_number ?: '-';
        $pdf->footerLeft = $issuerName . ' | ' . $docNum;

        // Ambil template background jika ada
        $this->cachedTemplatePath = $this->resolveTemplatePath();
        if ($this->cachedTemplatePath && file_exists($this->cachedTemplatePath)) {
            try {
                $pdf->setSourceFile($this->cachedTemplatePath);
                $this->importedTemplate = $pdf->importPage(1);
                $this->importedSize = $pdf->getTemplateSize($this->importedTemplate);
            } catch (\Throwable) {
                $this->importedTemplate = null;
                $this->importedSize = [];
            }
        }

        // Buka Halaman 1 lengkap dengan template kop surat & watermark
        $this->addNewPage($pdf);

        $this->renderInvoice($pdf, $invoice);

        return $pdf;
    }

    /**
     * Tambahkan halaman baru otomatis dengan template kop surat dan watermark.
     */
    public function addNewPage(Fpdi $pdf): void
    {
        if ($this->importedTemplate && !empty($this->importedSize)) {
            $pdf->AddPage($this->importedSize['orientation'] ?? 'P', [$this->importedSize['width'] ?? 210, $this->importedSize['height'] ?? 297]);
            $pdf->useTemplate($this->importedTemplate, 0, 0, $this->importedSize['width'] ?? 210, $this->importedSize['height'] ?? 297);
        } else {
            $pdf->AddPage('P', [210, 297]);
        }
    }

    /**
     * Render seluruh konten invoice di atas template halaman.
     * Mengikuti layout persis sample invoice (hardcode fallback siap didinamiskan).
     */
    protected function renderInvoice(Fpdi $pdf, Invoice $invoice): void
    {
        $pdf->SetTitle('Invoice_' . $invoice->invoice_number);
        $pdf->SetTextColor(0, 0, 0);

        // Data Dokumen & Identitas (Dinamis dari model Invoice)
        $invoiceNumber = $invoice->invoice_number ?: '-';
        $invoiceDateFormatted = 'Jakarta, ' . ($invoice->invoice_date ? $invoice->formatted_invoice_date : ($invoice->created_at ? $invoice->created_at->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y')));
        $companyName = $invoice->company_name ?: '-';
        $address = $invoice->address ?: '-';
        $phone = $invoice->phone ?: '-';
        $rawMenu = trim((string) ($invoice->menu_type ?? ''));
        if (str_starts_with(strtolower($rawMenu), 'meeting -')) {
            $menuSuffix = trim(substr($rawMenu, 9));
        } elseif (str_starts_with(strtolower($rawMenu), 'meeting')) {
            $menuSuffix = ltrim(trim(substr($rawMenu, 7)), '- ');
        } else {
            $menuSuffix = $rawMenu;
        }
        $menuType = 'Meeting' . (!empty($menuSuffix) ? ' - ' . $menuSuffix : '');
        $paymentMethod = $invoice->payment_method ?: '-';
        $activityTheme = $invoice->activity_theme ?: '-';

        // 1. JUDUL INVOICE & NOMOR (Tengah - Berjarak dari kop surat)
        $pdf->SetFont('Arial', 'B', 17);
        $pdf->SetXY(15, 52);
        $pdf->Cell(180, 7, 'INVOICE', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(15, 59);
        $pdf->Cell(180, 5, 'No. ' . $invoiceNumber, 0, 1, 'C');

        // 2. TANGGAL (Kanan)
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(120, 67);
        $pdf->Cell(75, 5, $invoiceDateFormatted, 0, 1, 'R');

        // 3. IDENTITAS & KEGIATAN
        // $startY = 74;
        $startY = 80;
        $lineH = 4.8;
        $labelW = 48;
        $colonX = 63;
        $valX = 67;

        $fields = [
            ['Nama Instansi / Perusahaan', $companyName],
            ['Alamat', $address],
            ['Telepon/ WA', $phone],
            ['Menu', $menuType],
            ['Pembayaran', $paymentMethod],
            ['Kegiatan', 'Bimbingan Teknis / Pelatihan'],
        ];

        $curY = $startY;
        foreach ($fields as $field) {
            $pdf->SetFont('Arial', 'B', 9.5);
            $pdf->SetXY(15, $curY);
            $pdf->Cell($labelW, $lineH, $field[0], 0, 0, 'L');
            $pdf->SetXY($colonX, $curY);
            $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

            if ($field[0] === 'Menu') {
                // Cetak "Meeting" (Bold)
                $pdf->SetFont('Arial', 'B', 9.5);
                $pdf->SetXY($valX, $curY);
                $meetingWidth = $pdf->GetStringWidth('Meeting');
                $pdf->Cell($meetingWidth, $lineH, 'Meeting', 0, 0, 'L');

                // Cetak lanjutannya (misal " - Offline") dengan Regular
                $pdf->SetFont('Arial', '', 9.5);
                $subText = !empty($menuSuffix) ? ' - ' . $menuSuffix : '';
                $pdf->Cell(128 - $meetingWidth, $lineH, $subText, 0, 1, 'L');
            } elseif ($field[0] === 'Kegiatan') {
                // Cetak "Bimbingan Teknis / Pelatihan" dengan Font Bold
                $pdf->SetFont('Arial', 'B', 9.5);
                $pdf->SetXY($valX, $curY);
                $pdf->Cell(128, $lineH, $field[1], 0, 1, 'L');
            } else {
                $pdf->SetFont('Arial', '', 9.5);
                $pdf->SetXY($valX, $curY);
                $pdf->Cell(128, $lineH, $field[1], 0, 1, 'L');
            }

            $curY += $lineH;
        }

        // Sub-judul kegiatan (Italic)
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY($valX, $curY - 0.5);
        $pdf->MultiCell(128, 4.4, $activityTheme, 0, 'L');
        $curY = $pdf->GetY() + 1.5;

        // 4. NAMA PESERTA (Dinamis dari model Invoice)
        $participantsList = [];
        if (!empty($invoice->participants) && is_array($invoice->participants)) {
            foreach ($invoice->participants as $p) {
                $pName = is_array($p) ? ($p['name'] ?? '') : (string) $p;
                if (!empty(trim($pName))) {
                    $participantsList[] = trim($pName);
                }
            }
        }

        $totalP = count($participantsList);

        if ($totalP === 0) {
            // Jika belum ada peserta, cetak strip (-) sejajar dengan label
            $pdf->SetFont('Arial', 'B', 9.5);
            $pdf->SetXY(15, $curY);
            $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
            $pdf->SetXY($colonX, $curY);
            $pdf->Cell(3, $lineH, ':', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9.5);
            $pdf->SetXY($valX, $curY);
            $pdf->Cell(128, $lineH, '-', 0, 1, 'L');
            $curY += $lineH + 3;
        } else {
            $half = (int) ceil($totalP / 2);
            $col1Peserta = array_slice($participantsList, 0, $half);
            $col2Peserta = array_slice($participantsList, $half);

            // Ukuran font dan tinggi baris peserta statis tetap
            $pH = 4.4;
            $fontSize = 9.5;
            $pesertaHeight = count($col1Peserta) * $pH;

            // Cek apakah seluruh daftar peserta muat di halaman saat ini:
            // Jika tidak muat (melebihi batas aman 265 mm) atau posisi saat ini sudah terlalu bawah (> 200 mm),
            // maka pindahkan seluruh blok Nama Peserta (Label, Titik Dua, dan seluruh list peserta) ke halaman baru bersama-sama ("pindah semua").
            if (($curY + $pesertaHeight > 265) || ($curY > 200 && $pesertaHeight > 25)) {
                $this->addNewPage($pdf);
                $curY = 50; // Mulai di bawah kop surat halaman baru
            }

            // Cetak Label "Nama Peserta" dan titik dua ":" sejajar dengan baris pertama peserta
            $pdf->SetFont('Arial', 'B', 9.5);
            $pdf->SetXY(15, $curY);
            $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
            $pdf->SetXY($colonX, $curY);
            $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

            $pesertaStartY = $curY;
            $pdf->SetFont('Arial', '', $fontSize);

            for ($i = 0; $i < count($col1Peserta); $i++) {
                $rowY = $pesertaStartY + ($i * $pH);

                // Jika daftar peserta sangat banyak sehingga melebihi batas bawah halaman (> 268 mm), buka halaman baru
                // dan cetak kembali label "Nama Peserta" agar selalu sejajar di halaman berikutnya
                if ($rowY + $pH > 268) {
                    $this->addNewPage($pdf);
                    $pdf->SetFont('Arial', 'B', 9.5);
                    $pdf->SetXY(15, 50);
                    $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
                    $pdf->SetXY($colonX, 50);
                    $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

                    $pdf->SetFont('Arial', '', $fontSize);
                    $pesertaStartY = 50 - ($i * $pH);
                    $rowY = 50;
                }

                // Kolom 1
                $pdf->SetXY($valX, $rowY);
                $pdf->Cell(62, $pH, ($i + 1) . '. ' . $col1Peserta[$i], 0, 0, 'L');

                // Kolom 2
                if (isset($col2Peserta[$i])) {
                    $pdf->SetXY(130, $rowY);
                    $pdf->Cell(65, $pH, ($half + $i + 1) . '. ' . $col2Peserta[$i], 0, 0, 'L');
                }
            }

            $curY = $rowY + $pH + 5;
        }

        // Cek apakah tabel rincian biaya, deposit, dan tanda tangan muat di sisa halaman (butuh ~102 mm)
        // Jika tidak muat (melebihi batas aman 275 mm sebelum footer 282 mm), otomatis pindah ke halaman baru lengkap dengan kop surat & watermark
        if ($curY + 102 > 275) {
            $this->addNewPage($pdf);
            $curY = 50; // Mulai di bawah kop surat pada halaman baru
        }

        // 5. TABEL RINCIAN BIAYA & JADWAL
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);

        $tableW = [
            'tanggal' => 30,
            'waktu' => 20,
            'lokasi' => 40,
            'jumlah' => 20,
            'harga' => 35,
            'total' => 35,
        ];

        // Header Tabel
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, $curY);
        $pdf->Cell($tableW['tanggal'], 8, 'Tanggal', 1, 0, 'C');
        $pdf->Cell($tableW['waktu'], 8, 'Waktu', 1, 0, 'C');
        $pdf->Cell($tableW['lokasi'], 8, 'Lokasi', 1, 0, 'C');
        $pdf->Cell($tableW['jumlah'], 8, 'Jumlah', 1, 0, 'C');
        $pdf->Cell($tableW['harga'], 8, 'Harga', 1, 0, 'C');
        $pdf->Cell($tableW['total'], 8, 'Total', 1, 1, 'C');

        // Data Baris Tabel
        $rowH = 14;
        $rowY = $pdf->GetY();
        $pdf->SetFont('Arial', '', 8.5);

        // Data Nilai Finansial (Dinamis dari model Invoice)
        $countVal = $invoice->participant_count > 0 ? $invoice->participant_count : ($totalP > 0 ? $totalP : 0);
        $priceVal = (float) $invoice->price_per_participant;
        $totalVal = (float) $invoice->total_amount > 0 ? (float) $invoice->total_amount : ($countVal * $priceVal);
        $depositVal = (float) $invoice->deposit_amount;
        $remainingVal = (float) $invoice->remaining_amount > 0 ? (float) $invoice->remaining_amount : ($totalVal - $depositVal);

        // Kolom Tanggal (2 baris dinamis dari rentang kegiatan)
        $dateLine1 = '-';
        $dateLine2 = '';
        if ($invoice->event_start_date) {
            $startD = Carbon::parse($invoice->event_start_date)->locale('id');
            if ($invoice->event_end_date && !$invoice->event_start_date->isSameDay($invoice->event_end_date)) {
                $endD = Carbon::parse($invoice->event_end_date)->locale('id');
                $dateLine1 = $startD->translatedFormat('d M. Y') . ' s.d';
                $dateLine2 = $endD->translatedFormat('d M. Y');
            } else {
                $dateLine1 = $startD->translatedFormat('d M. Y');
                $dateLine2 = '';
            }
        }

        $pdf->Rect(15, $rowY, $tableW['tanggal'], $rowH);
        if ($dateLine2 !== '') {
            $pdf->SetXY(15, $rowY + 2.5);
            $pdf->Cell($tableW['tanggal'], 4, $dateLine1, 0, 1, 'C');
            $pdf->SetXY(15, $rowY + 7);
            $pdf->Cell($tableW['tanggal'], 4, $dateLine2, 0, 0, 'C');
        } else {
            $pdf->SetXY(15, $rowY + 5);
            $pdf->Cell($tableW['tanggal'], 4, $dateLine1, 0, 0, 'C');
        }

        // Kolom Waktu
        $xPos = 15 + $tableW['tanggal'];
        $pdf->Rect($xPos, $rowY, $tableW['waktu'], $rowH);
        $pdf->SetXY($xPos, $rowY + 5);
        $pdf->Cell($tableW['waktu'], 4, $invoice->event_time ?: '-', 0, 0, 'C');

        // Kolom Lokasi
        $xPos += $tableW['waktu'];
        $pdf->Rect($xPos, $rowY, $tableW['lokasi'], $rowH);
        $locLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $invoice->event_location ?: '-'));
        $locLine1 = trim($locLines[0] ?? '-');
        $locLine2 = trim($locLines[1] ?? '');
        if ($locLine2 !== '') {
            $pdf->SetXY($xPos, $rowY + 2.5);
            $pdf->Cell($tableW['lokasi'], 4, $locLine1, 0, 1, 'C');
            $pdf->SetXY($xPos, $rowY + 7);
            $pdf->Cell($tableW['lokasi'], 4, $locLine2, 0, 0, 'C');
        } else {
            $pdf->SetXY($xPos, $rowY + 5);
            $pdf->Cell($tableW['lokasi'], 4, $locLine1, 0, 0, 'C');
        }

        // Kolom Jumlah
        $xPos += $tableW['lokasi'];
        $pdf->Rect($xPos, $rowY, $tableW['jumlah'], $rowH);
        $pdf->SetXY($xPos, $rowY + 5);
        $pdf->Cell($tableW['jumlah'], 4, (string) $countVal, 0, 0, 'C');

        // Kolom Harga
        $xPos += $tableW['jumlah'];
        $pdf->Rect($xPos, $rowY, $tableW['harga'], $rowH);
        $pdf->SetXY($xPos, $rowY + 5);
        $pdf->Cell($tableW['harga'], 4, 'Rp. ' . number_format($priceVal, 0, ',', '.') . ',-', 0, 0, 'C');

        // Kolom Total
        $xPos += $tableW['harga'];
        $pdf->Rect($xPos, $rowY, $tableW['total'], $rowH);
        $pdf->SetXY($xPos, $rowY + 5);
        $pdf->Cell($tableW['total'], 4, 'Rp. ' . number_format($totalVal, 0, ',', '.') . ',-', 0, 0, 'C');

        // Baris Terbilang
        $terbilangY = $rowY + $rowH;
        $pdf->Rect(15, $terbilangY, 180, 7.5);
        $pdf->SetXY(15, $terbilangY);
        $pdf->SetFont('Arial', 'I', 9);
        $terbilangText = $invoice->terbilang_total ?: '';
        if (empty($terbilangText) && function_exists('terbilang') && $totalVal > 0) {
            $terbilangText = ucwords(terbilang($totalVal)) . ' Rupiah';
        }
        if (empty($terbilangText)) {
            $terbilangText = $totalVal > 0 ? (number_format($totalVal, 0, ',', '.') . ' Rupiah') : 'Nol Rupiah';
        }
        $pdf->Cell(177, 7.5, 'Total Terbilang : ' . $terbilangText, 0, 1, 'R');

        // 6. DEPOSIT & SISA PEMBAYARAN
        $depY = $terbilangY + 11;
        $pdf->SetFont('Arial', 'B', 9.5);

        // Kiri
        $pdf->SetXY(15, $depY);
        $pdf->Cell(45, 5, 'Deposit', 0, 1, 'L');
        $pdf->SetXY(15, $depY + 5.5);
        $pdf->Cell(45, 5, 'Sisa Pembayaran', 0, 1, 'L');

        // Kanan
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY(135, $depY);
        $pdf->Cell(60, 5, 'Rp. ' . number_format($depositVal, 0, ',', '.') . ',-', 0, 1, 'R');
        $pdf->SetXY(135, $depY + 5.5);
        $pdf->Cell(60, 5, 'Rp. ' . number_format($remainingVal, 0, ',', '.') . ',-', 0, 1, 'R');

        // 7. TANDA TANGAN (HORMAT KAMI & BENDAHARA PELAKSANA)
        $ttdY = $depY + 22;
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(135, $ttdY);
        $pdf->Cell(60, 5, 'Hormat Kami', 0, 1, 'C');

        $bendaharaY = $ttdY + 24;
        $pdf->SetXY(135, $bendaharaY);
        $pdf->Cell(60, 5, 'Bendahara Pelaksana', 0, 1, 'C');
        $pdf->Line(140, $bendaharaY, 190, $bendaharaY);
    }

    /**
     * Mengambil path template PDF invoice dari SystemSetting.
     */
    protected function resolveTemplatePath(): ?string
    {
        $configuredPath = trim((string) system_setting('temp_linkpemda_invoice', ''));

        if (empty($configuredPath)) {
            return null;
        }

        // Jika URL
        if (str_starts_with($configuredPath, 'http://') || str_starts_with($configuredPath, 'https://')) {
            $filename = basename(parse_url($configuredPath, PHP_URL_PATH) ?: '');
            if (empty($filename)) {
                $filename = 'invoice_tpl_' . md5($configuredPath) . '.pdf';
            }

            // Cek lokal di uploads
            $localUpload = public_path('uploads/' . $filename);
            if (file_exists($localUpload) && filesize($localUpload) > 0) {
                return $localUpload;
            }

            // Cek cache
            $cacheDir = storage_path('app/pdf-templates-cache');
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            $cachedFile = $cacheDir . '/' . md5($configuredPath) . '_' . $filename;

            // Jika cache ada, cek apakah ukuran file di remote berubah
            if (file_exists($cachedFile) && filesize($cachedFile) > 0) {
                try {
                    $ch = curl_init($configuredPath);
                    curl_setopt_array($ch, [
                        CURLOPT_NOBODY => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 2,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ]);
                    curl_exec($ch);
                    $remoteSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

                    // Jika remote size sama dengan cache lokal, langsung gunakan cache
                    if ($remoteSize > 0 && filesize($cachedFile) == $remoteSize) {
                        return $cachedFile;
                    }
                } catch (\Throwable) {
                    return $cachedFile;
                }
            }

            // Download file terbaru
            try {
                $ch = curl_init($configuredPath);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                ]);
                $data = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($data !== false && $httpCode === 200 && strlen($data) > 0) {
                    file_put_contents($cachedFile, $data);
                    return $cachedFile;
                }
            } catch (\Throwable) {
            }

            if (file_exists($cachedFile) && filesize($cachedFile) > 0) {
                return $cachedFile;
            }

            return null;
        }

        if (file_exists($configuredPath)) {
            return $configuredPath;
        }

        $publicPath = public_path(ltrim($configuredPath, '/\\'));
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }
}
