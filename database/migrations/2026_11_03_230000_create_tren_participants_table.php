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
        Schema::create('tren_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Sertifikat & Verifikasi (Ember disiapkan untuk masa depan)
            $table->string('certificate_number')->nullable()->unique();
            $table->string('qr_code_path')->nullable();

            // Data Pribadi & Kontak Peserta
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 50)->nullable();
            $table->string('position')->nullable();
            $table->string('institution_name')->nullable()->index();

            // Data Kegiatan / Bimtek Pelatihan
            $table->longText('theme')->nullable();
            $table->string('certificate_date')->nullable();
            $table->string('event_venue')->nullable();
            $table->string('event_location')->nullable();
            $table->string('registration_date')->nullable();

            // Data Kwitansi Pembayaran
            $table->string('receipt_number')->nullable()->index();
            $table->string('receipt_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);

            // Tracking Pengiriman E-Sertifikat via Email (Ember disiapkan)
            $table->timestamp('email_sent_at')->nullable();
            $table->string('email_status')->default('pending')->index();

            // Catatan Tambahan
            $table->text('notes')->nullable();

            // Audit Trail (created_by, updated_by, deleted_by, timestamps, softDeletes)
            $table->auditTrail();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tren_participants');
    }
};
