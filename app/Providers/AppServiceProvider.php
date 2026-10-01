<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelMobilePass\Events\MobilePassAdded;
use Spatie\LaravelMobilePass\Events\MobilePassRemoved;

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

        Event::listen(MobilePassAdded::class, function (MobilePassAdded $event): void {
            $event->mobilePass->forceFill([
                'wallet_added_at' => now(),
                'wallet_removed_at' => null,
            ])->saveQuietly();
        });

        Event::listen(MobilePassRemoved::class, function (MobilePassRemoved $event): void {
            $event->mobilePass->forceFill([
                'wallet_removed_at' => now(),
            ])->saveQuietly();
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
            fn(): ?Password => app()->isProduction()
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
