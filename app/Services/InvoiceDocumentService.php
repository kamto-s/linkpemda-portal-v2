<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class InvoiceDocumentService
{
    /**
     * Menyiapkan instance FPDI dan menggambar seluruh elemen Invoice di atas template A4 Portrait.
     */
    public function buildPdf(Invoice $invoice): Fpdi
    {
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(15, 15, 15);

        // Ambil template background jika ada
        $templatePath = $this->resolveTemplatePath();
        if ($templatePath && file_exists($templatePath)) {
            try {
                $pdf->setSourceFile($templatePath);
                $template = $pdf->importPage(1);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'] ?? 'P', [$size['width'] ?? 210, $size['height'] ?? 297]);
                $pdf->useTemplate($template, 0, 0, $size['width'] ?? 210, $size['height'] ?? 297);
            } catch (\Throwable) {
                $pdf->AddPage('P', [210, 297]);
            }
        } else {
            $pdf->AddPage('P', [210, 297]);
        }

        $this->renderInvoice($pdf, $invoice);

        return $pdf;
    }

    /**
     * Render seluruh konten invoice sesuai template visual presisi.
     */
    protected function renderInvoice(Fpdi $pdf, Invoice $invoice): void
    {
        $pdf->SetTitle('Invoice_' . $invoice->invoice_number);

        // Warna & Font Standar
        $textColorDark = [30, 30, 30];
        $textColorRed = [170, 35, 35]; // Warna merah bata/maroon khas invoice contoh
        $grayBorder = [100, 100, 100];
        $separatorLine = [180, 195, 185];

        // ---------------------------------------------------------------------
        // 1. HEADER (Logo & Judul INVOICE)
        // ---------------------------------------------------------------------
        $logoPath = public_path('assets/images/linkpemda.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 12, 62);
        } else {
            // Fallback teks jika file logo belum ada
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor(15, 110, 50);
            $pdf->SetXY(15, 14);
            $pdf->Cell(100, 7, 'LINKPEMDA', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->SetTextColor(60, 60, 60);
            $pdf->SetX(15);
            $pdf->Cell(100, 4, 'Lembaga Informasi Keuangan dan Pembangunan Daerah', 0, 1, 'L');
        }

        // Judul INVOICE di kanan atas
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(20, 20, 20);
        $pdf->SetXY(130, 18);
        $pdf->Cell(65, 8, 'INVOICE', 0, 1, 'R');

        // Garis Pemisah Horizontal Header
        $pdf->SetDrawColor($separatorLine[0], $separatorLine[1], $separatorLine[2]);
        $pdf->SetLineWidth(0.6);
        $pdf->Line(15, 33, 195, 33);

        // ---------------------------------------------------------------------
        // 2. SEKRETARIAT (KIRI) vs TANGGAL & NO INVOICE (KANAN)
        // ---------------------------------------------------------------------
        // Kiri: Info Sekretariat
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetFont('Arial', 'B', 9.5);
        $pdf->SetXY(15, 36);
        $pdf->Cell(100, 5, 'Sekretariat Pusat :', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetX(15);
        $pdf->Cell(100, 4.2, 'Rosma Cipta Indah II G7, Ds. Setiamulya, Kec.', 0, 1, 'L');
        $pdf->SetX(15);
        $pdf->Cell(100, 4.2, 'Tarumajaya, Kab. Bekasi 17213', 0, 1, 'L');

        $kontakY = $pdf->GetY() + 1;
        $pdf->SetXY(15, $kontakY);
        $pdf->Cell(18, 4, 'Phone', 0, 0, 'L');
        $pdf->Cell(4, 4, ':', 0, 0, 'C');
        $pdf->Cell(70, 4, '+6282241312016', 0, 1, 'L');

        $pdf->SetX(15);
        $pdf->Cell(18, 4, 'Fax', 0, 0, 'L');
        $pdf->Cell(4, 4, ':', 0, 0, 'C');
        $pdf->Cell(70, 4, '021-88997129', 0, 1, 'L');

        $pdf->SetX(15);
        $pdf->Cell(18, 4, 'Email', 0, 0, 'L');
        $pdf->Cell(4, 4, ':', 0, 0, 'C');
        $pdf->Cell(70, 4, 'info@linkpemda.com', 0, 1, 'L');

        $pdf->SetX(15);
        $pdf->Cell(18, 4, 'Website', 0, 0, 'L');
        $pdf->Cell(4, 4, ':', 0, 0, 'C');
        $pdf->SetTextColor(15, 75, 175);
        $pdf->Cell(70, 4, 'www.linkpemda.com', 0, 1, 'L');

        // Kanan: Tanggal & No. Invoice
        $pdf->SetXY(120, 36);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->Cell(75, 5, 'Tanggal :', 0, 1, 'R');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->SetX(120);
        $invoiceDateStr = $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->locale('id')->translatedFormat('j F Y') : '-';
        $pdf->Cell(75, 5, $invoiceDateStr, 0, 1, 'R');

        $pdf->SetY($pdf->GetY() + 4);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetX(120);
        $pdf->Cell(75, 5, 'No. Invoice :', 0, 1, 'R');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->SetX(120);
        $pdf->Cell(75, 5, $invoice->invoice_number, 0, 1, 'R');

        // ---------------------------------------------------------------------
        // 3. DETAIL DATA PELANGGAN & KEGIATAN (BILL TO)
        // ---------------------------------------------------------------------
        $currY = 74;
        $labelX = 20;
        $labelW = 34;
        $colonW = 4;
        $valX = $labelX + $labelW + $colonW;
        $valW = 195 - $valX;

        // Nama Perusahaan / Instansi
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Nama Perusahaan', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->MultiCell($valW, 5, $invoice->company_name ?? '-', 0, 'L');
        $currY = $pdf->GetY() + 0.5;

        // Nama Peserta (Bisa banyak / dinamis)
        $participants = (array) ($invoice->participants ?? []);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Nama Peserta', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');

        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        if (! empty($participants)) {
            $num = 1;
            $startY = $currY;
            foreach ($participants as $pName) {
                if ($num > 1) {
                    $pdf->SetXY($valX, $pdf->GetY());
                } else {
                    $pdf->SetXY($valX, $startY);
                }
                $pdf->Cell(5, 5, "{$num}.", 0, 0, 'L');
                $pdf->MultiCell($valW - 5, 5, trim((string) $pName), 0, 'L');
                $num++;
            }
        } else {
            $pdf->SetXY($valX, $currY);
            $pdf->MultiCell($valW, 5, '-', 0, 'L');
        }
        $currY = $pdf->GetY() + 0.5;

        // Alamat
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Alamat', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->MultiCell($valW, 5, $invoice->address ?: '-', 0, 'L');
        $currY = $pdf->GetY() + 0.5;

        // Telepon
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Telepon', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->MultiCell($valW, 5, $invoice->phone ?: '-', 0, 'L');
        $currY = $pdf->GetY() + 0.5;

        // Kegiatan
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Kegiatan', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $kegiatanText = $invoice->activity_theme ?? '-';
        if (! str_starts_with(strtolower($kegiatanText), 'bimbingan teknis') && ! str_starts_with(strtolower($kegiatanText), 'pelatihan')) {
            $kegiatanText = "Bimbingan Teknis :\n" . $kegiatanText;
        }
        $pdf->MultiCell($valW, 5, $kegiatanText, 0, 'L');
        $currY = $pdf->GetY() + 0.5;

        // Menu
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Menu', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $menuLabel = match (strtolower($invoice->menu_type ?? '')) {
            'online' => 'Online',
            'offline' => 'Offline',
            default => ucfirst($invoice->menu_type ?? 'Meeting'),
        };
        $pdf->Cell($valW, 5, $menuLabel, 0, 1, 'L');
        $currY = $pdf->GetY() + 0.5;

        // Pembayaran
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($labelX, $currY);
        $pdf->Cell($labelW, 5, 'Pembayaran', 0, 0, 'L');
        $pdf->Cell($colonW, 5, ':', 0, 0, 'C');
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->Cell($valW, 5, $invoice->payment_method ?: 'Transfer / Cash', 0, 1, 'L');
        $currY = $pdf->GetY() + 6;

        // ---------------------------------------------------------------------
        // 4. TABEL RINCIAN KEGIATAN & TOTAL
        // ---------------------------------------------------------------------
        $tableX = 15;
        $tableY = max($currY, 138);

        // Lebar Kolom Tabel (Total = 180 mm)
        $wTgl = 32;
        $wWaktu = 28;
        $wLokasi = 48;
        $wJml = 18;
        $wHarga = 26;
        $wTotal = 28;

        $pdf->SetDrawColor($grayBorder[0], $grayBorder[1], $grayBorder[2]);
        $pdf->SetLineWidth(0.3);

        // Header Tabel
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($tableX, $tableY);
        $pdf->Cell($wTgl, 7, 'Tanggal', 1, 0, 'C');
        $pdf->Cell($wWaktu, 7, 'Waktu', 1, 0, 'C');
        $pdf->Cell($wLokasi, 7, 'Lokasi', 1, 0, 'C');
        $pdf->Cell($wJml, 7, 'Jumlah', 1, 0, 'C');
        $pdf->Cell($wHarga, 7, 'Harga', 1, 0, 'C');
        $pdf->Cell($wTotal, 7, 'Total', 1, 1, 'C');

        // Isi Baris Tabel
        $rowY = $tableY + 7;
        $rowH = 26; // Tinggi proporsional seperti di contoh gambar

        $pdf->Rect($tableX, $rowY, $wTgl, $rowH);
        $pdf->Rect($tableX + $wTgl, $rowY, $wWaktu, $rowH);
        $pdf->Rect($tableX + $wTgl + $wWaktu, $rowY, $wLokasi, $rowH);
        $pdf->Rect($tableX + $wTgl + $wWaktu + $wLokasi, $rowY, $wJml, $rowH);
        $pdf->Rect($tableX + $wTgl + $wWaktu + $wLokasi + $wJml, $rowY, $wHarga, $rowH);
        $pdf->Rect($tableX + $wTgl + $wWaktu + $wLokasi + $wJml + $wHarga, $rowY, $wTotal, $rowH);

        // Kolom 1: Tanggal
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->SetXY($tableX, $rowY + 2);
        if ($invoice->event_start_date && $invoice->event_end_date && ! $invoice->event_start_date->isSameDay($invoice->event_end_date)) {
            $tgl1 = Carbon::parse($invoice->event_start_date)->locale('id')->translatedFormat('j M. Y');
            $tgl2 = Carbon::parse($invoice->event_end_date)->locale('id')->translatedFormat('j M. Y');
            $pdf->MultiCell($wTgl, 4.5, "{$tgl1}\ns.d\n{$tgl2}", 0, 'C');
        } else {
            $tglSingle = $invoice->event_start_date ? Carbon::parse($invoice->event_start_date)->locale('id')->translatedFormat('j M. Y') : '-';
            $pdf->MultiCell($wTgl, 4.5, $tglSingle, 0, 'C');
        }

        // Kolom 2: Waktu
        $pdf->SetXY($tableX + $wTgl, $rowY + 2);
        $pdf->Cell($wWaktu, 5, $invoice->event_time ?: '08.00 WIB', 0, 0, 'C');

        // Kolom 3: Lokasi
        $pdf->SetXY($tableX + $wTgl + $wWaktu + 2, $rowY + 2);
        $pdf->MultiCell($wLokasi - 4, 4.5, $invoice->event_location ?: '-', 0, 'L');

        // Kolom 4: Jumlah
        $pdf->SetXY($tableX + $wTgl + $wWaktu + $wLokasi, $rowY + 2);
        $pdf->Cell($wJml, 5, (string) ($invoice->participant_count ?: count($participants)), 0, 0, 'C');

        // Kolom 5: Harga Satuan
        $pdf->SetXY($tableX + $wTgl + $wWaktu + $wLokasi + $wJml, $rowY + 2);
        $hargaFormatted = 'Rp. ' . number_format((float) $invoice->price_per_participant, 0, ',', '.') . ',-';
        $pdf->Cell($wHarga - 2, 5, $hargaFormatted, 0, 0, 'R');

        // Kolom 6: Total
        $pdf->SetXY($tableX + $wTgl + $wWaktu + $wLokasi + $wJml + $wHarga, $rowY + 2);
        $totalFormatted = 'Rp. ' . number_format((float) $invoice->total_amount, 0, ',', '.') . ',-';
        $pdf->Cell($wTotal - 2, 5, $totalFormatted, 0, 0, 'R');

        // Baris Total Terbilang
        $terbilangY = $rowY + $rowH;
        $terbilangH = 8;
        $pdf->Rect($tableX, $terbilangY, 180, $terbilangH);

        $pdf->SetFont('Arial', 'BI', 9);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY($tableX, $terbilangY + 1.5);
        $terbilangText = 'Total Terbilang : ' . $invoice->terbilang_total;
        $pdf->Cell(178, 5, $terbilangText, 0, 0, 'R');

        // ---------------------------------------------------------------------
        // 5. DEPOSIT & SISA PEMBAYARAN (KIRI) vs NILAI RUPIAH (KANAN)
        // ---------------------------------------------------------------------
        $sumY = $terbilangY + $terbilangH + 8;

        // Label
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetXY(20, $sumY);
        $pdf->Cell(45, 5, 'Deposit', 0, 1, 'L');

        $pdf->SetXY(20, $sumY + 5);
        $pdf->Cell(45, 5, 'Sisa Pembayaran', 0, 1, 'L');

        // Nilai Nominal (Rata Kanan)
        $pdf->SetTextColor($textColorRed[0], $textColorRed[1], $textColorRed[2]);
        $pdf->SetFont('Arial', '', 9.5);
        $pdf->SetXY(140, $sumY);
        $depositVal = (float) $invoice->deposit_amount;
        $depositStr = $depositVal > 0 ? 'Rp. ' . number_format($depositVal, 0, ',', '.') . ',-' : 'Rp. 0-';
        $pdf->Cell(45, 5, $depositStr, 0, 1, 'L');

        $pdf->SetXY(140, $sumY + 5);
        $sisaVal = (float) $invoice->remaining_amount;
        $sisaStr = 'Rp. ' . number_format($sisaVal, 0, ',', '.') . ',-';
        $pdf->Cell(45, 5, $sisaStr, 0, 1, 'L');

        // ---------------------------------------------------------------------
        // 6. TANDA TANGAN (HORMAT KAMI)
        // ---------------------------------------------------------------------
        $ttdY = $sumY + 22;
        $pdf->SetTextColor($textColorDark[0], $textColorDark[1], $textColorDark[2]);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(135, $ttdY);
        $pdf->Cell(50, 5, 'Hormat Kami', 0, 1, 'C');
        // Area bawah dikosongkan untuk tanda tangan basah & stempel sesuai permintaan user
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
            if (! is_dir($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            $cachedFile = $cacheDir . '/' . md5($configuredPath) . '_' . $filename;
            if (file_exists($cachedFile) && filesize($cachedFile) > 0) {
                return $cachedFile;
            }

            // Download
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
                curl_close($ch);

                if ($data !== false && $httpCode === 200 && strlen($data) > 0) {
                    file_put_contents($cachedFile, $data);
                    return $cachedFile;
                }
            } catch (\Throwable) {
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

