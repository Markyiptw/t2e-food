<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use PostHog\PostHog;

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
        Http::macro('openrice', function () {
            return Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->retry([100, 500, 1000])
                ->baseUrl('https://www.openrice.com/api/v2');
        });

        if (config('posthog.api_key')) {
            PostHog::init(
                config('posthog.api_key'),
                ['host' => config('posthog.host')] // default to US host
            );
        }
    }
}
