<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharifuddin\LaravelSmartModelTracker\Traits\SmartModelTracker;

class TestModel extends Model
{
    use SmartModelTracker, SoftDeletes;

    protected $table = 'test_models';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public $timestamps = false; // We handle timestamps manually in the trait
}
