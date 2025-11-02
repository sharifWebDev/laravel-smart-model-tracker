<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Facades;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static int|null getCurrentUserId()
 * @method static string|null getCurrentGuard()
 * @method static bool isTrackingEnabled()
 * @method static bool isTimestampTrackingEnabled()
 * @method static bool isUserTrackingEnabled()
 * @method static array getTrackingColumns()
 * @method static string getUserModelClass()
 * @method static Carbon getCurrentTimestamp()
 * @method static string formatTimestamp(Carbon $timestamp)
 * @method static array getAvailableGuards()
 * @method static bool guardHasUser(string $guard)
 * @method static int|null getUserIdFromGuard(string $guard)
 * @method static mixed getCurrentUser()
 * @method static bool isSoftDeletesIntegrationEnabled()
 * @method static bool isTrackingColumn(string $column)
 * @method static string|null getTrackingColumnName(string $type)
 * @method static array getTimestampColumns()
 * @method static array getUserTrackingColumns()
 * @method static bool isLoggingEnabled()
 * @method static void log(string $message, string $level = 'warning')
 * @method static string getVersion()
 * @method static array getConfig()
 * @method static void resetForTesting()
 *
 * @see \Sharifuddin\LaravelSmartModelTracker\SmartModelTracker
 */
final class SmartModelTracker extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'smart-model-tracker';
    }
}
