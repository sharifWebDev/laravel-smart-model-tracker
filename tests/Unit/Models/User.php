<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public $timestamps = false;
}
