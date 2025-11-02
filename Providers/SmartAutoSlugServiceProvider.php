<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Providers;

use Illuminate\Support\ServiceProvider;
use Sharifuddin\LaravelSmartModelTracker\SmartModelTracker;

final class SmartModelTrackerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/smart-model-tracker.php',
            'smart-model-tracker'
        );

        $this->registerFacades();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishResources();
    }

    /**
     * Register package facades.
     */
    private function registerFacades(): void
    {
        $this->app->singleton('smart-model-tracker', function ($app) {
            return new SmartModelTracker;
        });
    }

    /**
     * Publish package resources.
     */
    private function publishResources(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/smart-model-tracker.php' => config_path('smart-model-tracker.php'),
            ], 'smart-model-tracker-config');
        }
    }
}
