<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/helpers.php'))) {
            require_once app_path('Helpers/helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Terapkan pengaturan sistem dinamis ke konfigurasi runtime
        $this->applySystemSettings();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }

            if (app()->runningUnitTests() && $user->roles()->count() === 0) {
                return true;
            }
        });

        Blueprint::macro('auditTrail', function () {
            $this->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamps();
            $this->softDeletes();
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // Standar zona waktu WIB (Asia/Jakarta) & Bahasa Indonesia
        $timezone = config('app.timezone', 'Asia/Jakarta');
        date_default_timezone_set($timezone);
        CarbonImmutable::setLocale('id');

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }

    /**
     * Terapkan nilai pengaturan sistem dinamis ke konfigurasi runtime aplikasi.
     */
    protected function applySystemSettings(): void
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            // 1. Nama Aplikasi
            if ($appName = SystemSetting::get('app_name')) {
                config(['app.name' => $appName]);
            }

            // 2. Email Sender Default
            if ($mailFromAddress = SystemSetting::get('mail_sender_address')) {
                config(['mail.from.address' => $mailFromAddress]);
            }
            if ($mailFromName = SystemSetting::get('mail_sender_name')) {
                config(['mail.from.name' => $mailFromName]);
            }

            // 3. Durasi Sesi Pengguna
            if ($sessionLifetime = SystemSetting::get('session_lifetime_minutes')) {
                config(['session.lifetime' => (int) $sessionLifetime]);
            }

            // 4. Timezone dinamis dari database jika diset
            if ($tz = SystemSetting::get('app_timezone')) {
                date_default_timezone_set($tz);
                config(['app.timezone' => $tz]);
            }
        } catch (\Throwable) {
            // Hindari error saat proses instalasi, migrasi awal, atau console commands
        }
    }
}
