<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sharifuddin\LaravelSmartModelTracker\Providers\SmartModelTrackerServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SmartModelTrackerServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup auth configuration
        $app['config']->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => \Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models\User::class,
        ]);

        // Load package configuration
        $app['config']->set('smart-model-tracker', require __DIR__.'/../config/smart-model-tracker.php');
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
