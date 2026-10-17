<?php

namespace App\Services;

use App\Enums\ContactCategory;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContactImportService
{
    /**
     * Import contacts from an Excel or CSV file.
     *
     * @param string $filePath
     * @param string $duplicateHandling 'update' or 'skip'
     * @return array
     */
    public function import(string $filePath, string $duplicateHandling = 'update'): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return [
                'total' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['File Excel/CSV kosong.'],
            ];
        }

        // Find header row and map columns
        $headerRowIndex = null;
        $columnMap = [];

        foreach ($rows as $index => $row) {
            $map = $this->mapHeaders($row);
            if (isset($map['name']) && isset($map['email'])) {
                $headerRowIndex = $index;
                $columnMap = $map;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return [
                'total' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['Format kolom tidak dikenali. Pastikan terdapat kolom "Nama", "Email", dan "Instansi".'],
            ];
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $totalRows = 0;

        $dataRows = array_slice($rows, $headerRowIndex + 1);

        DB::beginTransaction();

        try {
            foreach ($dataRows as $offset => $row) {
                $rowNumber = $headerRowIndex + $offset + 2; // 1-indexed for human readability

                // Check if row is completely empty
                $nonEmpty = array_filter($row, fn ($val) => $val !== null && trim((string) $val) !== '');
                if (empty($nonEmpty)) {
                    continue;
                }

                $totalRows++;

                $name = trim((string) ($row[$columnMap['name']] ?? ''));
                $email = strtolower(trim((string) ($row[$columnMap['email']] ?? '')));
                $organization = isset($columnMap['organization_name']) ? trim((string) ($row[$columnMap['organization_name']] ?? '')) : '';
                $position = isset($columnMap['position']) ? trim((string) ($row[$columnMap['position']] ?? '')) : null;
                $phone = isset($columnMap['phone']) ? trim((string) ($row[$columnMap['phone']] ?? '')) : null;
                $categoryRaw = isset($columnMap['category']) ? trim((string) ($row[$columnMap['category']] ?? '')) : 'pemerintah';
                $statusRaw = isset($columnMap['is_active']) ? trim((string) ($row[$columnMap['is_active']] ?? '')) : '1';

                // Validation
                if ($name === '') {
                    $errors[] = "Baris #{$rowNumber}: Nama kontak tidak boleh kosong.";
                    continue;
                }

                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris #{$rowNumber}: Format email \"{$email}\" tidak valid.";
                    continue;
                }

                if ($organization === '') {
                    $organization = 'Umum / Lainnya';
                }

                // Normalize category
                $category = str_contains(strtolower($categoryRaw), 'swasta')
                    ? ContactCategory::Swasta
                    : ContactCategory::Pemerintah;

                // Normalize is_active
                $lowerStatus = strtolower($statusRaw);
                $isActive = ! (
                    str_contains($lowerStatus, 'non') ||
                    str_contains($lowerStatus, 'tidak') ||
                    $lowerStatus === '0' ||
                    $lowerStatus === 'false' ||
                    $lowerStatus === 'inactive'
                );

                $existing = Contact::where('email', $email)->first();

                if ($existing) {
                    if ($duplicateHandling === 'update') {
                        $existing->update([
                            'name' => $name,
                            'organization_name' => $organization,
                            'position' => $position ?: $existing->position,
                            'phone' => $phone ?: $existing->phone,
                            'category' => $category,
                            'is_active' => $isActive,
                        ]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    Contact::create([
                        'name' => $name,
                        'email' => $email,
                        'organization_name' => $organization,
                        'position' => $position,
                        'phone' => $phone,
                        'category' => $category,
                        'is_active' => $isActive,
                    ]);
                    $imported++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'total' => $totalRows,
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Map row headers to standardized contact fields.
     */
    protected function mapHeaders(array $row): array
    {
        $map = [];

        foreach ($row as $colIndex => $value) {
            if ($value === null) {
                continue;
            }

            $clean = strtolower(trim((string) $value));
            $clean = preg_replace('/[^a-z0-9]/', '', $clean);

            if (str_contains($clean, 'email') || str_contains($clean, 'surel') || str_contains($clean, 'mail')) {
                $map['email'] = $colIndex;
            } elseif (str_contains($clean, 'nama') || str_contains($clean, 'pic') || str_contains($clean, 'name') || $clean === 'kontak') {
                $map['name'] = $colIndex;
            } elseif (str_contains($clean, 'instansi') || str_contains($clean, 'perusahaan') || str_contains($clean, 'lembaga') || str_contains($clean, 'organi') || str_contains($clean, 'kantor') || str_contains($clean, 'opd') || str_contains($clean, 'dinas') || str_contains($clean, 'badan')) {
                $map['organization_name'] = $colIndex;
            } elseif (str_contains($clean, 'jabatan') || str_contains($clean, 'posisi') || str_contains($clean, 'position') || str_contains($clean, 'bagian')) {
                $map['position'] = $colIndex;
            } elseif (str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'phone') || str_contains($clean, 'wa') || str_contains($clean, 'hp')) {
                $map['phone'] = $colIndex;
            } elseif (str_contains($clean, 'kategori') || str_contains($clean, 'category') || str_contains($clean, 'tipe') || str_contains($clean, 'jenis')) {
                $map['category'] = $colIndex;
            } elseif (str_contains($clean, 'status') || str_contains($clean, 'aktif') || str_contains($clean, 'active')) {
                $map['is_active'] = $colIndex;
            }
        }

        return $map;
    }

    /**
     * Generate sample Excel template for contacts.
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kontak');

        // Headers
        $headers = [
            'A1' => 'Nama Kontak (PIC) *',
            'B1' => 'Email *',
            'C1' => 'Instansi / Perusahaan *',
            'D1' => 'Jabatan',
            'E1' => 'No. Telepon / WhatsApp',
            'F1' => 'Kategori (Pemerintah/Swasta)',
            'G1' => 'Status (Aktif/Nonaktif)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];

        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows
        $samples = [
            [
                'Bpk. Hendra Gunawan, S.Kom',
                'hendra.gunawan@bappeda.go.id',
                'Bappeda Provinsi Jawa Barat',
                'Kepala Bidang Perencanaan & Evaluasi',
                '081234567890',
                'Pemerintah',
                'Aktif',
            ],
            [
                'Ibu Maya Anggraini, S.T',
                'maya@solusidigital.co.id',
                'PT Solusi Digital Mandiri',
                'Direktur Operasional',
                '085678901234',
                'Swasta',
                'Aktif',
            ],
            [
                'Drs. H. Mulyadi, M.Si',
                'mulyadi@dinasdiklat.go.id',
                'BKPSDM Kabupaten Bandung',
                'Sekretaris Badan',
                '081398765432',
                'Pemerintah',
                'Aktif',
            ],
        ];

        $rowIndex = 2;
        foreach ($samples as $sample) {
            $colLetter = 'A';
            foreach ($sample as $val) {
                $sheet->setCellValueExplicit($colLetter . $rowIndex, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $colLetter++;
            }
            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        // Borders for data rows
        $dataBorderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:G4')->applyFromArray($dataBorderStyle);

        // Auto size columns
        foreach (range(1, 7) as $colIndex) {
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        }

        // Output to string
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
