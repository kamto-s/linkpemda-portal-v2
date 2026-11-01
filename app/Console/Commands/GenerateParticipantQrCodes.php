<?php

namespace App\Console\Commands;

use App\Models\Participant;
use Illuminate\Console\Command;

class GenerateParticipantQrCodes extends Command
{
    protected $signature = 'participants:generate-qr
                            {--force : Re-generate meskipun file QR sudah ada}';

    protected $description = 'Generate file PNG QR Code untuk semua peserta yang belum memiliki file QR di public/uploads/qr_code/';

    public function handle(): int
    {
        $participants = Participant::whereNotNull('certificate_number')->get();

        if ($participants->isEmpty()) {
            $this->warn('Tidak ada data peserta ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$participants->count()} peserta. Memulai proses generate QR Code...");
        $bar = $this->output->createProgressBar($participants->count());
        $bar->start();

        $generated = 0;
        $skipped   = 0;
        $failed    = 0;

        foreach ($participants as $participant) {
            $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number);
            $filePath  = public_path("uploads/qr_code/{$sanitized}.png");

            if (! $this->option('force') && file_exists($filePath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $result = Participant::generateQrCodeFile($participant);
            if ($result) {
                $generated++;
            } else {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai: {$generated} QR Code di-generate, {$skipped} dilewati (sudah ada), {$failed} gagal.");

        return self::SUCCESS;
    }
}

