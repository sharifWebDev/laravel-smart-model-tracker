<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

final class SmartModelTracker
{
    /**
     * Get the current authenticated user ID.
     */
    public function getCurrentUserId(): ?int
    {
        try {
            $guards = array_keys(Config::get('auth.guards', []));

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return Auth::guard($guard)->id();
                }
            }

            return Auth::id();
        } catch (\Throwable $e) {
            if (config('smart-model-tracker.enable_logging', true)) {
                logger()->warning('SmartModelTracker: Failed to get current user ID - '.$e->getMessage());
            }

            return null;
        }
    }

    /**
     * Get the current authentication guard name.
     */
    public function getCurrentGuard(): ?string
    {
        try {
            $guards = array_keys(Config::get('auth.guards', []));

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return $guard;
                }
            }

            return null;
        } catch (\Throwable $e) {
            if (config('smart-model-tracker.enable_logging', true)) {
                logger()->warning('SmartModelTracker: Failed to get current guard - '.$e->getMessage());
            }

            return null;
        }
    }

    /**
     * Check if user tracking is currently enabled.
     */
    public function isTrackingEnabled(): bool
    {
        return Auth::check();
    }

    /**
     * Check if timestamp tracking is enabled in configuration.
     */
    public function isTimestampTrackingEnabled(): bool
    {
        return config('smart-model-tracker.enable_timestamps', true);
    }

    /**
     * Check if user tracking is enabled in configuration.
     */
    public function isUserTrackingEnabled(): bool
    {
        return config('smart-model-tracker.enable_user_tracking', true);
    }

    /**
     * Get the configured tracking columns.
     */
    public function getTrackingColumns(): array
    {
        return config('smart-model-tracker.columns', [
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'created_by' => 'created_by',
            'updated_by' => 'updated_by',
            'deleted_by' => 'deleted_by',
        ]);
    }

    /**
     * Get the user model class.
     */
    public function getUserModelClass(): string
    {
        return config('smart-model-tracker.user_model', Config::get('auth.providers.users.model', 'App\\Models\\User'));
    }

    /**
     * Get the current timestamp for tracking.
     */
    public function getCurrentTimestamp(): Carbon
    {
        return Carbon::now();
    }

    /**
     * Format a timestamp according to configuration.
     */
    public function formatTimestamp(Carbon $timestamp): string
    {
        $format = config('smart-model-tracker.timestamp_format');

        return $format ? $timestamp->format($format) : $timestamp->toDateTimeString();
    }

    /**
     * Get all available authentication guards.
     */
    public function getAvailableGuards(): array
    {
        return array_keys(Config::get('auth.guards', []));
    }

    /**
     * Check if a specific guard has an authenticated user.
     */
    public function guardHasUser(string $guard): bool
    {
        try {
            return Auth::guard($guard)->check();
        } catch (\Throwable $e) {
            if (config('smart-model-tracker.enable_logging', true)) {
                logger()->warning("SmartModelTracker: Failed to check guard '{$guard}' - ".$e->getMessage());
            }

            return false;
        }
    }

    /**
     * Get the user ID from a specific guard.
     */
    public function getUserIdFromGuard(string $guard): ?int
    {
        try {
            return Auth::guard($guard)->check() ? Auth::guard($guard)->id() : null;
        } catch (\Throwable $e) {
            if (config('smart-model-tracker.enable_logging', true)) {
                logger()->warning("SmartModelTracker: Failed to get user from guard '{$guard}' - ".$e->getMessage());
            }

            return null;
        }
    }

    /**
     * Get the current user instance.
     */
    public function getCurrentUser()
    {
        try {
            $guards = array_keys(Config::get('auth.guards', []));

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return Auth::guard($guard)->user();
                }
            }

            return Auth::user();
        } catch (\Throwable $e) {
            if (config('smart-model-tracker.enable_logging', true)) {
                logger()->warning('SmartModelTracker: Failed to get current user - '.$e->getMessage());
            }

            return null;
        }
    }

    /**
     * Check if soft deletes integration is enabled.
     */
    public function isSoftDeletesIntegrationEnabled(): bool
    {
        return config('smart-model-tracker.soft_deletes_integration', true);
    }

    /**
     * Validate if a column name is a standard tracking column.
     */
    public function isTrackingColumn(string $column): bool
    {
        $trackingColumns = $this->getTrackingColumns();

        return in_array($column, $trackingColumns, true);
    }

    /**
     * Get the default tracking column name for a given type.
     */
    public function getTrackingColumnName(string $type): ?string
    {
        $columns = $this->getTrackingColumns();

        return $columns[$type] ?? null;
    }

    /**
     * Get all timestamp tracking columns.
     */
    public function getTimestampColumns(): array
    {
        $columns = $this->getTrackingColumns();

        return [
            'created_at' => $columns['created_at'] ?? 'created_at',
            'updated_at' => $columns['updated_at'] ?? 'updated_at',
        ];
    }

    /**
     * Get all user tracking columns.
     */
    public function getUserTrackingColumns(): array
    {
        $columns = $this->getTrackingColumns();

        return [
            'created_by' => $columns['created_by'] ?? 'created_by',
            'updated_by' => $columns['updated_by'] ?? 'updated_by',
            'deleted_by' => $columns['deleted_by'] ?? 'deleted_by',
        ];
    }

    /**
     * Check if logging is enabled.
     */
    public function isLoggingEnabled(): bool
    {
        return config('smart-model-tracker.enable_logging', true);
    }

    /**
     * Log a message if logging is enabled.
     */
    public function log(string $message, string $level = 'warning'): void
    {
        if ($this->isLoggingEnabled() && function_exists('logger')) {
            logger()->log($level, "SmartModelTracker: {$message}");
        }
    }

    /**
     * Get package version information.
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * Get package configuration.
     */
    public function getConfig(): array
    {
        return config('smart-model-tracker', []);
    }

    /**
     * Reset the authentication state for testing purposes.
     */
    public function resetForTesting(): void
    {
        // This method is primarily for testing
        if (app()->environment('testing')) {
            Auth::logout();
        }
    }
}
