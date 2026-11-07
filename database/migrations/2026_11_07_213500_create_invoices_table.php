<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas Dokumen
            $table->string('invoice_number', 100)->unique()->index();
            $table->date('invoice_date');

            // Data Pelanggan / Instansi
            $table->string('company_name');
            $table->string('email')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('phone', 100)->nullable();

            // Data Kegiatan / Bimtek
            $table->text('activity_theme');
            $table->string('menu_type', 50)->default('Offline'); // Online / Offline / Meeting
            $table->string('payment_method', 100)->default('Transfer / Cash');

            // Jadwal & Lokasi Pelaksanaan
            $table->date('event_start_date');
            $table->date('event_end_date')->nullable();
            $table->string('event_time', 100)->default('08.00 WIB');
            $table->string('event_location');

            // Peserta & Rincian Finansial
            $table->json('participants'); // Array nama peserta
            $table->unsignedInteger('participant_count')->default(1);
            $table->decimal('price_per_participant', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);

            // 1. Status Persetujuan Admin (ACC Berlevel)
            // pending (menunggu acc) / approved (disetujui) / rejected (ditolak)
            $table->string('status', 30)->default('pending')->index();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();

            // 2. Status Pembayaran Terpisah
            // unpaid (belum lunas) / partial (dp) / paid (lunas)
            $table->string('payment_status', 30)->default('unpaid')->index();

            // 3. Pelacakan Pengiriman Email
            $table->timestamp('email_sent_at')->nullable();

            // Catatan
            $table->text('notes')->nullable();

            // Audit Trail
            $table->auditTrail();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
