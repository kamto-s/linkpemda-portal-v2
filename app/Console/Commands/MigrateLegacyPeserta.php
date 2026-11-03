<?php

namespace App\Console\Commands;

use App\Models\Participant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateLegacyPeserta extends Command
{
    protected $signature = 'participants:import-legacy
                            {--file= : Path ke file JSON atau CSV hasil export tabel pesertas lama}
                            {--table=pesertas : Nama tabel lama jika berada di database yang sama}
                            {--connection= : Nama koneksi database lama jika menggunakan database terpisah}';

    protected $description = 'Migrasi data peserta dari sistem Linkpemda lama ke tabel participants sistem baru';

    public function handle(): int
    {
        $filePath = $this->option('file');

        if ($filePath) {
            return $this->importFromFile($filePath);
        }

        return $this->importFromDatabase();
    }

    protected function importFromDatabase(): int
    {
        $tableName = $this->option('table');
        $connection = $this->option('connection');

        $this->info("Mencoba membaca data dari tabel [{$tableName}]...");

        try {
            $db = $connection ? DB::connection($connection) : DB::connection();
            $records = $db->table($tableName)->get();
        } catch (\Throwable $e) {
            $this->error('Gagal mengakses database/tabel lama: ' . $e->getMessage());
            $this->line('Tips: Anda juga bisa mengekspor tabel lama ke file JSON atau CSV, lalu jalankan:');
            $this->line('php artisan participants:import-legacy --file=path/ke/file.json');
            return self::FAILURE;
        }

        if ($records->isEmpty()) {
            $this->warn("Tabel [{$tableName}] kosong.");
            return self::SUCCESS;
        }

        return $this->processRecords($records);
    }

    protected function importFromFile(string $filePath): int
    {
        if (! file_exists($filePath)) {
            $this->error("File [{$filePath}] tidak ditemukan.");
            return self::FAILURE;
        }

        $this->info("Membaca file data: {$filePath}...");
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (! is_array($data)) {
            $this->error('Format file tidak valid. Pastikan file berupa JSON array dari baris data.');
            return self::FAILURE;
        }

        return $this->processRecords(collect($data));
    }

    protected function processRecords($records): int
    {
        $total = $records->count();
        $this->info("Memulai migrasi {$total} data peserta...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $migrated = 0;
        $skipped = 0;

        foreach ($records as $row) {
            $row = (array) $row;

            $certNumber = $row['no_certf'] ?? $row['certificate_number'] ?? null;
            if (empty($certNumber)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Bersihkan format biaya jika berupa string (misal: "Rp. 5.000.000" -> 5000000)
            $amount = 0;
            if (! empty($row['biaya'])) {
                $rawAmount = preg_replace('/[^0-9]/', '', (string) $row['biaya']);
                $amount = ! empty($rawAmount) ? (float) $rawAmount : 0;
            } elseif (! empty($row['amount'])) {
                $amount = (float) $row['amount'];
            }

            // Cek apakah data sudah ada berdasarkan nomor sertifikat
            $existing = Participant::where('certificate_number', $certNumber)->first();

            $data = [
                'name'               => $row['name'] ?? 'Peserta',
                'position'           => $row['jabatan'] ?? $row['position'] ?? null,
                'institution_name'   => $row['instansi'] ?? $row['institution_name'] ?? null,
                'theme'              => $row['tema'] ?? $row['theme'] ?? null,
                'certificate_date'   => $row['tgl_sertifikat'] ?? $row['certificate_date'] ?? null,
                'event_venue'        => $row['tempat_kegiatan'] ?? $row['event_venue'] ?? null,
                'event_location'     => $row['lokasi'] ?? $row['event_location'] ?? null,
                'registration_date'  => $row['tgl_registrasi'] ?? $row['registration_date'] ?? null,
                'receipt_number'     => $row['receipt_number'] ?? $certNumber,
                'receipt_date'       => $row['tgl_kwitansi'] ?? $row['receipt_date'] ?? null,
                'amount'             => $amount,
                'created_at'         => $row['created_at'] ?? now(),
                'updated_at'         => $row['updated_at'] ?? now(),
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                $data['id'] = (string) Str::uuid();
                $data['certificate_number'] = $certNumber;
                $newParticipant = Participant::create($data);

                // Otomatis buatkan file gambar QR Code jika belum ada
                Participant::generateQrCodeFile($newParticipant);
            }

            $migrated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Selesai! {$migrated} data peserta berhasil dimigrasi ({$skipped} dilewati karena tanpa nomor sertifikat).");
        return self::SUCCESS;
    }
}

