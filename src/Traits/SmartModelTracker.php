<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

/**
 * @mixin Model
 */
trait SmartModelTracker
{
    /**
     * Boot the SmartModelTracker trait.
     */
    public static function bootSmartModelTracker(): void
    {
        static::creating(function (Model $model): void {
            $model->setTimestampFields();
            $model->setUserTrackingFields();
        });

        static::updating(function (Model $model): void {
            $model->setUpdatedTimestampField();
            $model->setUpdatedByField();
        });

        static::deleting(function (Model $model): void {
            $model->setDeletedByField();
        });

        static::restoring(function (Model $model): void {
            $model->clearDeletedByField();
        });
    }

    /**
     * Set timestamp fields during model creation.
     */
    protected function setTimestampFields(): void
    {
        $currentTime = $this->freshTimestamp();

        if ($this->hasTrackingColumn('created_at') && empty($this->created_at)) {
            $this->created_at = $currentTime;
        }

        if ($this->hasTrackingColumn('updated_at')) {
            $this->updated_at = $currentTime;
        }
    }

    /**
     * Set updated timestamp field during model update.
     */
    protected function setUpdatedTimestampField(): void
    {
        if ($this->hasTrackingColumn('updated_at')) {
            $this->updated_at = $this->freshTimestamp();
        }
    }

    /**
     * Set user tracking fields during model creation.
     */
    protected function setUserTrackingFields(): void
    {
        $userId = self::resolveAuthUserId();

        if (! $userId) {
            return;
        }

        if ($this->hasTrackingColumn('created_by') && empty($this->created_by)) {
            $this->created_by = $userId;
        }

        if ($this->hasTrackingColumn('updated_by')) {
            $this->updated_by = $userId;
        }
    }

    /**
     * Set updated_by field during model update.
     */
    protected function setUpdatedByField(): void
    {
        $userId = self::resolveAuthUserId();

        if ($userId && $this->hasTrackingColumn('updated_by')) {
            $this->updated_by = $userId;
        }
    }

    /**
     * Set deleted_by field during model deletion.
     */
    protected function setDeletedByField(): void
    {
        $userId = self::resolveAuthUserId();

        if ($userId && $this->hasTrackingColumn('deleted_by')) {
            $this->deleted_by = $userId;

            // Use saveQuietly to prevent recursion and avoid model events
            if (method_exists($this, 'saveQuietly')) {
                $this->saveQuietly();
            } else {
                // Fallback for older Laravel versions
                $originalTimestamps = $this->timestamps;
                $this->timestamps = false;
                $this->save();
                $this->timestamps = $originalTimestamps;
            }
        }
    }

    /**
     * Clear deleted_by field during model restoration.
     */
    protected function clearDeletedByField(): void
    {
        if ($this->hasTrackingColumn('deleted_by')) {
            $this->deleted_by = null;
        }
    }

    /**
     * Detect the currently authenticated user's ID by dynamically checking all guards.
     */
    protected static function resolveAuthUserId(): ?int
    {
        try {
            $guards = array_keys(Config::get('auth.guards', []));

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return Auth::guard($guard)->id();
                }
            }

            // Fallback to default guard
            return Auth::id();
        } catch (\Throwable $e) {
            // Log the error but don't break the application
            if (function_exists('logger')) {
                logger()->warning('SmartModelTracker guard detection failed: '.$e->getMessage());
            }

            return null;
        }
    }

    /**
     * Check if a given tracking column exists on the model's table.
     */
    protected function hasTrackingColumn(string $column): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), $column);
        } catch (\Throwable $e) {
            // Log schema errors but don't break the application
            if (function_exists('logger')) {
                logger()->warning('SmartModelTracker column check failed: '.$e->getMessage());
            }

            return false;
        }
    }

    /**
     * Get the user who created the model.
     */
    public function creator()
    {
        if (! $this->hasTrackingColumn('created_by') || ! $this->created_by) {
            return null;
        }

        $userModel = Config::get('auth.providers.users.model', 'App\\Models\\User');

        return $this->belongsTo($userModel, 'created_by');
    }

    /**
     * Get the user who last updated the model.
     */
    public function updater()
    {
        if (! $this->hasTrackingColumn('updated_by') || ! $this->updated_by) {
            return null;
        }

        $userModel = Config::get('auth.providers.users.model', 'App\\Models\\User');

        return $this->belongsTo($userModel, 'updated_by');
    }

    /**
     * Get the user who deleted the model.
     */
    public function deleter()
    {
        if (! $this->hasTrackingColumn('deleted_by') || ! $this->deleted_by) {
            return null;
        }

        $userModel = Config::get('auth.providers.users.model', 'App\\Models\\User');

        return $this->belongsTo($userModel, 'deleted_by');
    }

    /**
     * Scope a query to only include records created by a specific user.
     */
    public function scopeCreatedBy($query, int $userId)
    {
        if (! $this->hasTrackingColumn('created_by')) {
            return $query;
        }

        return $query->where('created_by', $userId);
    }

    /**
     * Scope a query to only include records updated by a specific user.
     */
    public function scopeUpdatedBy($query, int $userId)
    {
        if (! $this->hasTrackingColumn('updated_by')) {
            return $query;
        }

        return $query->where('updated_by', $userId);
    }

    /**
     * Scope a query to only include records deleted by a specific user.
     */
    public function scopeDeletedBy($query, int $userId)
    {
        if (! $this->hasTrackingColumn('deleted_by')) {
            return $query;
        }

        return $query->where('deleted_by', $userId);
    }

    /**
     * Scope a query to only include records created after a specific date.
     */
    public function scopeCreatedAfter($query, $date)
    {
        if (! $this->hasTrackingColumn('created_at')) {
            return $query;
        }

        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope a query to only include records created before a specific date.
     */
    public function scopeCreatedBefore($query, $date)
    {
        if (! $this->hasTrackingColumn('created_at')) {
            return $query;
        }

        return $query->where('created_at', '<=', $date);
    }

    /**
     * Scope a query to only include records updated after a specific date.
     */
    public function scopeUpdatedAfter($query, $date)
    {
        if (! $this->hasTrackingColumn('updated_at')) {
            return $query;
        }

        return $query->where('updated_at', '>=', $date);
    }

    /**
     * Scope a query to only include records updated before a specific date.
     */
    public function scopeUpdatedBefore($query, $date)
    {
        if (! $this->hasTrackingColumn('updated_at')) {
            return $query;
        }

        return $query->where('updated_at', '<=', $date);
    }

    /**
     * Manually update the timestamps and user tracking fields.
     */
    public function touchWithTracking(): bool
    {
        $this->setUpdatedTimestampField();
        $this->setUpdatedByField();

        return $this->save();
    }

    /**
     * Get the creation date in a formatted string.
     */
    public function getCreatedAtFormatted(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->created_at ? $this->created_at->format($format) : null;
    }

    /**
     * Get the update date in a formatted string.
     */
    public function getUpdatedAtFormatted(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->updated_at ? $this->updated_at->format($format) : null;
    }

    /**
     * Check if the model was created by a specific user.
     */
    public function wasCreatedBy(int $userId): bool
    {
        return $this->hasTrackingColumn('created_by') && $this->created_by === $userId;
    }

    /**
     * Check if the model was updated by a specific user.
     */
    public function wasUpdatedBy(int $userId): bool
    {
        return $this->hasTrackingColumn('updated_by') && $this->updated_by === $userId;
    }

    /**
     * Check if the model was deleted by a specific user.
     */
    public function wasDeletedBy(int $userId): bool
    {
        return $this->hasTrackingColumn('deleted_by') && $this->deleted_by === $userId;
    }
}
