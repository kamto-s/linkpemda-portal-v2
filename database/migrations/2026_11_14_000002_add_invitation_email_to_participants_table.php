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
        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'invitation_sent_at')) {
                $table->timestamp('invitation_sent_at')->nullable()->after('email_status');
            }
            if (!Schema::hasColumn('participants', 'invitation_status')) {
                $table->string('invitation_status')->default('pending')->index()->after('invitation_sent_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('participants', 'invitation_sent_at')) {
                $columns[] = 'invitation_sent_at';
            }
            if (Schema::hasColumn('participants', 'invitation_status')) {
                $columns[] = 'invitation_status';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

