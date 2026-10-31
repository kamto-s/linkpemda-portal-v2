<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

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
}
