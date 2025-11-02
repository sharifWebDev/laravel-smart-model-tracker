<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Sharifuddin\LaravelSmartModelTracker\Traits\SmartModelTracker;

class CustomTrackedModel extends Model
{
    use SmartModelTracker;

    protected $table = 'custom_tracked_models';

    protected $fillable = [
        'title',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public $timestamps = true;
}
