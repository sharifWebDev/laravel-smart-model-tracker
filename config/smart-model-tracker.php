<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Tracking Columns
    |--------------------------------------------------------------------------
    |
    | This value defines the default column names used for tracking user
    | actions and timestamps on the model. You can customize these per model if needed.
    |
    */
    'columns' => [
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'created_by' => 'created_by',
        'updated_by' => 'updated_by',
        'deleted_by' => 'deleted_by',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Timestamp Tracking
    |--------------------------------------------------------------------------
    |
    | This value determines if the package should automatically handle
    | timestamp fields (created_at, updated_at).
    |
    */
    'enable_timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable User Tracking
    |--------------------------------------------------------------------------
    |
    | This value determines if the package should automatically handle
    | user tracking fields (created_by, updated_by, deleted_by).
    |
    */
    'enable_user_tracking' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Soft Deletes Integration
    |--------------------------------------------------------------------------
    |
    | This value determines if the package should automatically integrate
    | with Laravel's soft deletes feature when tracking deletion events.
    |
    */
    'soft_deletes_integration' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This value defines the user model class that will be used for
    | relationships. By default, it uses Laravel's configured user model.
    |
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Default Guard
    |--------------------------------------------------------------------------
    |
    | This value defines the default authentication guard to use when
    | resolving the current user. Set to null to auto-detect.
    |
    */
    'default_guard' => null,

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | This value determines if failed authentication or schema checks
    | should be logged. This is useful for debugging but can be disabled
    | in production for better performance.
    |
    */
    'enable_logging' => true,

    /*
    |--------------------------------------------------------------------------
    | Timestamp Format
    |--------------------------------------------------------------------------
    |
    | This value defines the default format for timestamp fields.
    | Set to null to use Laravel's default format.
    |
    */
    'timestamp_format' => null,

];
