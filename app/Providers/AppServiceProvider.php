<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register MikrotikService as singleton
        $this->app->singleton(\App\Services\MikrotikService::class);

        // Register facade alias
        $this->app->alias(\App\Facades\Mikrotik::class, 'Mikrotik');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
