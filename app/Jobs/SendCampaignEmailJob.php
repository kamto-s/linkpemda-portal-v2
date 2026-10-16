<?php

namespace App\Jobs;

use App\Enums\CampaignStatus;
use App\Enums\RecipientStatus;
use App\Mail\CampaignEmail;
use App\Models\EmailCampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan maksimal jika terjadi kegagalan
     */
    public int $tries = 2;

    /**
     * Timeout job dalam detik
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public EmailCampaignRecipient $recipient
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->recipient->loadMissing(['campaign', 'contact']);

        $campaign = $this->recipient->campaign;
        $contact = $this->recipient->contact;

        if (!$campaign || !$contact || !$contact->is_active) {
            $this->recipient->update([
                'status' => RecipientStatus::Failed,
                'error_message' => 'Kontak tidak aktif atau data campaign tidak ditemukan.',
            ]);
            return;
        }

        // Set status campaign menjadi processing jika masih queued
        if ($campaign->status === CampaignStatus::Queued) {
            $campaign->update(['status' => CampaignStatus::Processing]);
        }

        try {
            // Kirim email
            Mail::to($contact->email)->send(new CampaignEmail($campaign, $contact));

            // Tandai berhasil
            $this->recipient->update([
                'status' => RecipientStatus::Sent,
                'sent_at' => now(),
                'error_message' => null,
            ]);

            $campaign->increment('sent_count');

        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email campaign ID {$campaign->id} ke {$contact->email}: " . $e->getMessage());

            // Tandai gagal
            $this->recipient->update([
                'status' => RecipientStatus::Failed,
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            $campaign->increment('failed_count');
        }

        // Shared hosting throttling: beri jeda 1 detik per pengiriman agar tidak terblokir limit SMTP
        sleep(1);

        // Periksa apakah semua email dalam campaign ini sudah selesai diproses
        $remaining = $campaign->recipients()->where('status', RecipientStatus::Pending->value)->count();
        if ($remaining === 0) {
            $campaign->refresh();
            $finalStatus = $campaign->sent_count > 0 ? CampaignStatus::Completed : CampaignStatus::Failed;
            $campaign->update([
                'status' => $finalStatus,
                'sent_at' => now(),
            ]);
        }
    }
}

