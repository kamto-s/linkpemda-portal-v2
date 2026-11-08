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
        $invoiceDateFormatted = ($invoice->invoice_date ? $invoice->formatted_invoice_date : ($invoice->created_at ? $invoice->created_at->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y')));
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
            $pH = 4.4;
            $fontSize = 9.5;
            $safeBottomY = 266; // Batas bawah aman konten sebelum footer (282 mm)

            // Hitung kapasitas baris maksimal yang muat di sisa Halaman 1
            $availableRowsPage1 = max(5, (int) floor(($safeBottomY - $curY) / $pH));

            // Kasus A: Seluruh peserta muat di Halaman 1 (dalam 2 kolom)
            if ($totalP <= $availableRowsPage1 * 2) {
                $half = (int) ceil($totalP / 2);
                $col1 = array_slice($participantsList, 0, $half);
                $col2 = array_slice($participantsList, $half);

                // Cetak Label "Nama Peserta"
                $pdf->SetFont('Arial', 'B', 9.5);
                $pdf->SetXY(15, $curY);
                $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
                $pdf->SetXY($colonX, $curY);
                $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

                $pdf->SetFont('Arial', '', $fontSize);
                for ($i = 0; $i < count($col1); $i++) {
                    $rowY = $curY + ($i * $pH);
                    $pdf->SetXY($valX, $rowY);
                    $pdf->Cell(62, $pH, ($i + 1) . '. ' . $col1[$i], 0, 0, 'L');

                    if (isset($col2[$i])) {
                        $pdf->SetXY(130, $rowY);
                        $pdf->Cell(65, $pH, ($half + $i + 1) . '. ' . $col2[$i], 0, 0, 'L');
                    }
                }
                $curY += count($col1) * $pH + 5;
            } else {
                // Kasus B: Jumlah peserta banyak, penuhi Halaman 1 secara maksimal lalu lanjutkan ke Halaman 2
                $page1Rows = $availableRowsPage1;
                $page1Count = $page1Rows * 2;
                $page1Items = array_slice($participantsList, 0, $page1Count);
                $remainingItems = array_slice($participantsList, $page1Count);

                $halfP1 = $page1Rows;
                $p1Col1 = array_slice($page1Items, 0, $halfP1);
                $p1Col2 = array_slice($page1Items, $halfP1);

                // Cetak Label "Nama Peserta" di Halaman 1
                $pdf->SetFont('Arial', 'B', 9.5);
                $pdf->SetXY(15, $curY);
                $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
                $pdf->SetXY($colonX, $curY);
                $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

                $pdf->SetFont('Arial', '', $fontSize);
                for ($i = 0; $i < count($p1Col1); $i++) {
                    $rowY = $curY + ($i * $pH);
                    $pdf->SetXY($valX, $rowY);
                    $pdf->Cell(62, $pH, ($i + 1) . '. ' . $p1Col1[$i], 0, 0, 'L');

                    if (isset($p1Col2[$i])) {
                        $pdf->SetXY(130, $rowY);
                        $pdf->Cell(65, $pH, ($halfP1 + $i + 1) . '. ' . $p1Col2[$i], 0, 0, 'L');
                    }
                }

                // Pindah ke Halaman 2 untuk mencetak sisa peserta lanjutan
                $this->addNewPage($pdf);
                $curY = 50; // Mulai di bawah kop surat halaman baru

                $offsetNumber = $page1Count;
                while (!empty($remainingItems)) {
                    $pageNextRows = (int) floor(($safeBottomY - $curY) / $pH);
                    $pageNextCapacity = max(2, $pageNextRows * 2);

                    $chunk = array_slice($remainingItems, 0, $pageNextCapacity);
                    $remainingItems = array_slice($remainingItems, $pageNextCapacity);

                    $halfNext = (int) ceil(count($chunk) / 2);
                    $chunkCol1 = array_slice($chunk, 0, $halfNext);
                    $chunkCol2 = array_slice($chunk, $halfNext);

                    // Cetak label "Nama Peserta" sejajar di halaman lanjutan
                    $pdf->SetFont('Arial', 'B', 9.5);
                    $pdf->SetXY(15, $curY);
                    $pdf->Cell($labelW, $lineH, 'Nama Peserta', 0, 0, 'L');
                    $pdf->SetXY($colonX, $curY);
                    $pdf->Cell(3, $lineH, ':', 0, 0, 'C');

                    $pdf->SetFont('Arial', '', $fontSize);
                    for ($i = 0; $i < count($chunkCol1); $i++) {
                        $rowY = $curY + ($i * $pH);
                        $pdf->SetXY($valX, $rowY);
                        $pdf->Cell(62, $pH, ($offsetNumber + $i + 1) . '. ' . $chunkCol1[$i], 0, 0, 'L');

                        if (isset($chunkCol2[$i])) {
                            $pdf->SetXY(130, $rowY);
                            $pdf->Cell(65, $pH, ($offsetNumber + $halfNext + $i + 1) . '. ' . $chunkCol2[$i], 0, 0, 'L');
                        }
                    }

                    $curY += count($chunkCol1) * $pH + 5;
                    $offsetNumber += count($chunk);

                    if (!empty($remainingItems)) {
                        $this->addNewPage($pdf);
                        $curY = 50;
                    }
                }
            }
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
            'waktu' => 22,
            'lokasi' => 48,
            'jumlah' => 14,
            'harga' => 33,
            'total' => 33,
        ];

        // Header Tabel
        $pdf->SetFont('Arial', 'B', 9);
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

        // Data Nilai Finansial (Dinamis dari model Invoice)
        $countVal = $invoice->participant_count > 0 ? $invoice->participant_count : ($totalP > 0 ? $totalP : 0);
        $priceVal = (float) $invoice->price_per_participant;
        $totalVal = (float) $invoice->total_amount > 0 ? (float) $invoice->total_amount : ($countVal * $priceVal);
        $depositVal = (float) $invoice->deposit_amount;
        $remainingVal = (float) $invoice->remaining_amount > 0 ? (float) $invoice->remaining_amount : ($totalVal - $depositVal);

        // 1. Kolom Tanggal (2 baris dinamis dari rentang kegiatan)
        $dateLine1 = '-';
        $dateLine2 = '';
        if ($invoice->event_start_date) {
            $startD = Carbon::parse($invoice->event_start_date)->locale('id');
            if ($invoice->event_end_date && !$invoice->event_start_date->isSameDay($invoice->event_end_date)) {
                $endD = Carbon::parse($invoice->event_end_date)->locale('id');
                $dateLine1 = $startD->translatedFormat('d M Y') . ' s.d';
                $dateLine2 = $endD->translatedFormat('d M Y');
            } else {
                $dateLine1 = $startD->translatedFormat('d M Y');
                $dateLine2 = '';
            }
        }

        $pdf->Rect(15, $rowY, $tableW['tanggal'], $rowH);
        $pdf->SetFont('Arial', '', 8.5);
        if ($dateLine2 !== '') {
            $pdf->SetXY(15, $rowY + 2.5);
            $pdf->Cell($tableW['tanggal'], 4.2, $dateLine1, 0, 1, 'C');
            $pdf->SetXY(15, $rowY + 7);
            $pdf->Cell($tableW['tanggal'], 4.2, $dateLine2, 0, 0, 'C');
        } else {
            $pdf->SetXY(15, $rowY + 4.8);
            $pdf->Cell($tableW['tanggal'], 4.5, $dateLine1, 0, 0, 'C');
        }

        // 2. Kolom Waktu (Di-wrap aman agar tidak pernah keluar batas kolom)
        $xPos = 15 + $tableW['tanggal'];
        $pdf->Rect($xPos, $rowY, $tableW['waktu'], $rowH);
        $pdf->SetFont('Arial', '', 8.5);
        $waktuLines = $this->wrapTextToWidth($pdf, $invoice->event_time ?: '-', $tableW['waktu'] - 2);
        if (count($waktuLines) >= 2) {
            $pdf->SetXY($xPos, $rowY + 2.5);
            $pdf->Cell($tableW['waktu'], 4.2, $waktuLines[0], 0, 1, 'C');
            $pdf->SetXY($xPos, $rowY + 7);
            $pdf->Cell($tableW['waktu'], 4.2, $waktuLines[1], 0, 0, 'C');
        } else {
            $pdf->SetXY($xPos, $rowY + 4.8);
            $pdf->Cell($tableW['waktu'], 4.5, $waktuLines[0] ?? '-', 0, 0, 'C');
        }

        // 3. Kolom Lokasi (Auto-wrap ketat di batas 48 mm, teks tidak akan pernah menabrak kolom lain)
        $xPos += $tableW['waktu'];
        $pdf->Rect($xPos, $rowY, $tableW['lokasi'], $rowH);
        $lokasiMaxW = $tableW['lokasi'] - 3; // 45 mm batas lebar teks
        $pdf->SetFont('Arial', '', 8);
        $lokasiLines = $this->wrapTextToWidth($pdf, $invoice->event_location ?: '-', $lokasiMaxW);

        if (count($lokasiLines) === 1) {
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->SetXY($xPos, $rowY + 4.8);
            $pdf->Cell($tableW['lokasi'], 4.5, $lokasiLines[0], 0, 0, 'C');
        } elseif (count($lokasiLines) === 2) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY($xPos, $rowY + 2.5);
            $pdf->Cell($tableW['lokasi'], 4.2, $lokasiLines[0], 0, 1, 'C');
            $pdf->SetXY($xPos, $rowY + 7);
            $pdf->Cell($tableW['lokasi'], 4.2, $lokasiLines[1], 0, 0, 'C');
        } else {
            // 3 baris
            $pdf->SetFont('Arial', '', 7.5);
            $line3 = $lokasiLines[2] ?? '';
            if (count($lokasiLines) > 3) {
                $line3 .= '...';
            }
            $pdf->SetXY($xPos, $rowY + 1.6);
            $pdf->Cell($tableW['lokasi'], 3.6, $lokasiLines[0], 0, 1, 'C');
            $pdf->SetXY($xPos, $rowY + 5.2);
            $pdf->Cell($tableW['lokasi'], 3.6, $lokasiLines[1], 0, 1, 'C');
            $pdf->SetXY($xPos, $rowY + 8.8);
            $pdf->Cell($tableW['lokasi'], 3.6, $line3, 0, 0, 'C');
        }

        // 4. Kolom Jumlah
        $xPos += $tableW['lokasi'];
        $pdf->Rect($xPos, $rowY, $tableW['jumlah'], $rowH);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY($xPos, $rowY + 4.8);
        $pdf->Cell($tableW['jumlah'], 4.5, (string) $countVal, 0, 0, 'C');

        // 5. Kolom Harga
        $xPos += $tableW['jumlah'];
        $pdf->Rect($xPos, $rowY, $tableW['harga'], $rowH);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetXY($xPos, $rowY + 4.8);
        $pdf->Cell($tableW['harga'], 4.5, 'Rp. ' . number_format($priceVal, 0, ',', '.') . ',-', 0, 0, 'C');

        // 6. Kolom Total
        $xPos += $tableW['harga'];
        $pdf->Rect($xPos, $rowY, $tableW['total'], $rowH);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetXY($xPos, $rowY + 4.8);
        $pdf->Cell($tableW['total'], 4.5, 'Rp. ' . number_format($totalVal, 0, ',', '.') . ',-', 0, 0, 'C');

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
     * Memecah teks menjadi baris-baris yang lebarnya dijamin muat dalam batas kolom (mm).
     * Mencegah teks meluber dan menabrak kolom di sebelahnya.
     */
    protected function wrapTextToWidth(Fpdi $pdf, string $text, float $maxWidth): array
    {
        $rawLines = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($text)));
        $finalLines = [];

        foreach ($rawLines as $rawLine) {
            $words = preg_split('/\s+/', trim($rawLine));
            if (empty($words) || (count($words) === 1 && $words[0] === '')) {
                continue;
            }

            $currentLine = '';
            foreach ($words as $word) {
                $test = $currentLine === '' ? $word : $currentLine . ' ' . $word;
                if ($pdf->GetStringWidth($test) <= $maxWidth) {
                    $currentLine = $test;
                } else {
                    if ($currentLine !== '') {
                        $finalLines[] = $currentLine;
                    }
                    // Jika satu kata melebihi lebar kolom, pangkas kata agar tidak tembus batas
                    if ($pdf->GetStringWidth($word) > $maxWidth) {
                        while (mb_strlen($word) > 1 && $pdf->GetStringWidth($word . '...') > $maxWidth) {
                            $word = mb_substr($word, 0, -1);
                        }
                        $word .= '...';
                    }
                    $currentLine = $word;
                }
            }
            if ($currentLine !== '') {
                $finalLines[] = $currentLine;
            }
        }

        return !empty($finalLines) ? $finalLines : ['-'];
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
