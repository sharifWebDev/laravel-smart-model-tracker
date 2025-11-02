# Laravel Smart Model Tracker

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/sharifuddin/laravel-smart-model-tracker)](https://packagist.org/packages/sharifuddin/laravel-smart-model-tracker)
[![Latest Version](https://img.shields.io/packagist/v/sharifuddin/laravel-smart-model-tracker)](https://packagist.org/packages/sharifuddin/laravel-smart-model-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/sharifuddin/laravel-smart-model-tracker)](https://packagist.org/packages/sharifuddin/laravel-smart-model-tracker)

A professional, robust, and seamless user tracking package for Laravel Eloquent models. Automatically tracks created_at, updated_at, created_by, updated_by, and deleted_by with zero boilerplate code.

## ✨ Features

🚀 Automatic Timestamp & User Tracking - Handles created_at, updated_at, created_by, updated_by, deleted_by

🔒 Multi-Guard Support - Works with any authentication guard

🔄 Soft Deletes Integration - Tracks who deleted models

🎯 Relationship Methods - Easy access to creator, updater, deleter

📊 Query Scopes - Filter by dates and users

🛡️ Error Resilient - Won't break your application on errors

⚡ Zero Configuration - Works out of the box

📦 PSR-4 Compliant - Professional code standards

🧪 Full Test Coverage - Reliable and well-tested

🔧 Laravel 8-12 Support - Full framework compatibility

## 📦 Installation

```bash
composer require sharifuddin/laravel-smart-model-tracker
```

## 🚀 Quick Start

**_ Basic Usage _**
**_Add the trait to your Eloquent model: _**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sharifuddin\LaravelSmartModelTracker\Traits\SmartModelTracker;

class Post extends Model
{
    use SmartModelTracker;

    protected $fillable = ['title', 'content'];
}

```

## With Soft Deletes

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharifuddin\LaravelSmartModelTracker\Traits\SmartModelTracker;

class Post extends Model
{
    use SmartModelTracker, SoftDeletes;

    protected $fillable = ['title', 'content'];
}
```

## Database Migration

Create a migration with tracking columns:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');

            // Timestamp columns (handled automatically)
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // User tracking columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->softDeletes();

            // Optional: Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

## 💡 Usage Examples

## Automatic Tracking

```php
// Creating a post (user is authenticated)
$post = Post::create([
    'title' => 'My First Post',
    'content' => 'Post content...'
]);

echo $post->created_at;    // Output: 2023-12-01 10:30:00
echo $post->updated_at;    // Output: 2023-12-01 10:30:00
echo $post->created_by;    // Output: 1 (current user ID)
echo $post->updated_by;    // Output: 1 (current user ID)

// Updating the post
$post->update(['title' => 'Updated Title']);
echo $post->updated_at;    // Output: 2023-12-01 10:35:00 (updated)
echo $post->updated_by;    // Output: 1 (still current user ID)

// Soft deleting the post
$post->delete();
echo $post->deleted_by;    // Output: 1 (current user ID)
echo $post->deleted_at;    // Output: 2023-12-01 10:40:00

// Restoring the post
$post->restore();
echo $post->deleted_by;    // Output: null (cleared on restore)
```

## Relationships

```php
$post = Post::first();

// Get the user who created the post
$creator = $post->creator;

// Get the user who last updated the post
$updater = $post->updater;

// Get the user who deleted the post (if soft deleted)
$deleter = $post->deleter;

// Usage in views
<h1>{{ $post->title }}</h1>
<p>Created by: {{ $post->creator->name }}</p>
<p>Last updated by: {{ $post->updater->name }}</p>
@if($post->trashed())
    <p>Deleted by: {{ $post->deleter->name }}</p>
@endif
```

## Query Scopes

```php
// User-based scopes
$userPosts = Post::createdBy(1)->get();
$updatedPosts = Post::updatedBy(1)->get();
$deletedPosts = Post::deletedBy(1)->get();

// Date-based scopes
$recentPosts = Post::createdAfter('2023-11-01')->get();
$oldPosts = Post::createdBefore('2023-11-01')->get();
$recentlyUpdated = Post::updatedAfter('2023-11-15')->get();

// Combined scopes
$userRecentPosts = Post::createdBy(1)
    ->createdAfter('2023-11-01')
    ->get();
```

## Advanced Features

```php
$post = Post::first();

// Formatted timestamps
echo $post->getCreatedAtFormatted('Y-m-d');        // Output: 2023-12-01
echo $post->getUpdatedAtFormatted('M d, Y');       // Output: Dec 01, 2023

// Ownership checks
if ($post->wasCreatedBy($currentUser->id)) {
    // User created this post
}

if ($post->wasUpdatedBy($currentUser->id)) {
    // User updated this post
}

if ($post->wasDeletedBy($currentUser->id)) {
    // User deleted this post
}

// Manual tracking update
$post->touchWithTracking(); // Updates updated_at and updated_by

```

## Configuration

## Publish the configuration file:

```bash
php artisan vendor:publish --tag=smart-model-tracker-config
```

## config/smart-model-tracker.php

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Tracking Columns
    |--------------------------------------------------------------------------
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
    */
    'enable_timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable User Tracking
    |--------------------------------------------------------------------------
    */
    'enable_user_tracking' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable Soft Deletes Integration
    |--------------------------------------------------------------------------
    */
    'soft_deletes_integration' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Default Guard
    |--------------------------------------------------------------------------
    */
    'default_guard' => null,

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    */
    'enable_logging' => true,

    /*
    |--------------------------------------------------------------------------
    | Timestamp Format
    |--------------------------------------------------------------------------
    */
    'timestamp_format' => null,
];
```

## Customization

Custom Column Names

```php
class Post extends Model
{
    use SmartModelTracker;

    protected $fillable = [
        'title',
        'content',
        'created_by_user_id',  // Custom created_by column
        'updated_by_user_id',  // Custom updated_by column
        'deleted_by_user_id'   // Custom deleted_by column
    ];
}
```

## Using the Facade

```php
use Sharifuddin\LaravelSmartModelTracker\Facades\SmartModelTracker;

// Get current tracking information
$userId = SmartModelTracker::getCurrentUserId();
$guard = SmartModelTracker::getCurrentGuard();
$user = SmartModelTracker::getCurrentUser();

// Check tracking status
if (SmartModelTracker::isTrackingEnabled()) {
// Perform tracking operations
}

// Get configuration
$columns = SmartModelTracker::getTrackingColumns();
$config = SmartModelTracker::getConfig();
```

## Multiple Authentication Guards

The package automatically works with multiple guards:

```php
// Works with web guard (default)
Auth::guard('web')->login($user);

// Works with API guard
Auth::guard('api')->login($user);

// Works with any custom guard
Auth::guard('admin')->login($adminUser);
```

## Testing

```bash
composer test
```

Run with coverage:

```bash
composer test-coverage
```

Code Quality

```bash
composer lint
```

Fix code style:

```bash
composer format
```

## 🔧 Compatibility

Laravel Version PHP Version Package Version
12.x 8.2+ ^1.0
11.x 8.2+ ^1.0
10.x 8.1+ ^1.0
9.x 8.0+ ^1.0
8.x 8.0+ ^1.0

---

## 📝 Changelog

Please see CHANGELOG.md for details.

## 🛡️ Security

If you discover any security-related issues, please email sharif.webpro@gmail.com instead of using the issue tracker.

## 📜 License

his package is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Author

**Sharif Uddin**

Email: sharif.webpro@gmail.com

Website: https://sharifwebdev.github.io/

## GitHub: @sharifwebdev

🙏 Acknowledgments
Inspired by the need for simple, robust model tracking in Laravel applications

Thanks to the Laravel community for best practices and inspiration

---

## ⭐ Star this repository if you find it helpful!
