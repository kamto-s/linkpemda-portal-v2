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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi Peserta
            $table->foreignUuid('participant_id')->constrained('participants')->cascadeOnDelete();

            // Tipe Sesi: 'registrasi', 'hari_1', 'hari_2', 'hari_3', dst.
            $table->string('session_type', 50)->index();

            // Waktu Check-in & Check-out
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();

            // Status: 'hadir', 'izin', 'sakit', 'alpha'
            $table->string('status', 20)->default('hadir')->index();

            // Panitia yang memverifikasi kehadiran
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Catatan Tambahan
            $table->text('notes')->nullable();

            // Audit Trail (created_by, updated_by, deleted_by, timestamps, softDeletes)
            $table->auditTrail();

            // 1 peserta hanya memiliki 1 catatan per session_type
            $table->unique(['participant_id', 'session_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

