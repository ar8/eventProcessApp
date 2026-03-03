<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
        // Define a rate limiter for the enrichment service to prevent overloading the external API
        RateLimiter::for('enrichment-service', function (): Limit {
            return Limit::perMinute(100)->by('enrichment-service-global');
        });
    }
}
