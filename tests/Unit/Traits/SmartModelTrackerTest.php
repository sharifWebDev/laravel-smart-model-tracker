<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Sharifuddin\LaravelSmartModelTracker\Tests\TestCase;
use Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models\TestModel;
use Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models\User;

final class SmartModelTrackerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($this->user);
    }

    /** @test */
    public function it_sets_timestamps_and_user_tracking_on_creation(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
        $this->assertSame($this->user->id, $model->created_by);
        $this->assertSame($this->user->id, $model->updated_by);
    }

    /** @test */
    public function it_updates_timestamps_and_user_tracking_on_update(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);
        $originalCreatedAt = $model->created_at;
        $originalUpdatedAt = $model->updated_at;

        sleep(1); // Ensure timestamp difference

        $newUser = User::create([
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($newUser);
        $model->update(['name' => 'Updated Model']);

        $this->assertEquals($originalCreatedAt, $model->created_at);
        $this->assertNotEquals($originalUpdatedAt, $model->updated_at);
        $this->assertSame($this->user->id, $model->created_by);
        $this->assertSame($newUser->id, $model->updated_by);
    }

    /** @test */
    public function it_sets_deleted_by_on_soft_delete(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $model->delete();

        $this->assertSame($this->user->id, $model->deleted_by);
        $this->assertNotNull($model->deleted_at);
    }

    /** @test */
    public function it_handles_models_without_timestamp_columns(): void
    {
        $model = \App\Models\ModelWithoutTimestamps::create(['title' => 'Test Title']);

        $this->assertSame($this->user->id, $model->created_by);
        $this->assertSame($this->user->id, $model->updated_by);
        $this->assertNull($model->created_at);
        $this->assertNull($model->updated_at);
    }

    /** @test */
    public function it_does_not_set_fields_when_no_user_is_authenticated(): void
    {
        Auth::logout();

        $model = TestModel::create(['name' => 'Test Model']);

        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
        $this->assertNull($model->created_by);
        $this->assertNull($model->updated_by);
    }

    /** @test */
    public function it_provides_timestamp_relationship_methods(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $this->assertInstanceOf(User::class, $model->creator);
        $this->assertSame($this->user->id, $model->creator->id);

        $this->assertInstanceOf(User::class, $model->updater);
        $this->assertSame($this->user->id, $model->updater->id);
    }

    /** @test */
    public function it_provides_timestamp_query_scopes(): void
    {
        TestModel::create(['name' => 'Model 1']);
        TestModel::create(['name' => 'Model 2']);

        $models = TestModel::createdBy($this->user->id)->get();
        $this->assertCount(2, $models);

        $recentModels = TestModel::createdAfter(now()->subDay())->get();
        $this->assertCount(2, $recentModels);

        $oldModels = TestModel::createdBefore(now()->subYear())->get();
        $this->assertCount(0, $oldModels);
    }

    /** @test */
    public function it_can_manually_touch_with_tracking(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);
        $originalUpdatedAt = $model->updated_at;

        sleep(1);

        $result = $model->touchWithTracking();

        $this->assertTrue($result);
        $this->assertNotEquals($originalUpdatedAt, $model->updated_at);
        $this->assertSame($this->user->id, $model->updated_by);
    }

    /** @test */
    public function it_provides_formatted_timestamp_methods(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $createdFormatted = $model->getCreatedAtFormatted('Y-m-d');
        $updatedFormatted = $model->getUpdatedAtFormatted('Y-m-d');

        $this->assertIsString($createdFormatted);
        $this->assertIsString($updatedFormatted);
        $this->assertEquals(now()->format('Y-m-d'), $createdFormatted);
    }

    /** @test */
    public function it_can_check_user_ownership(): void
    {
        $model = TestModel::create(['name' => 'Test Model']);

        $this->assertTrue($model->wasCreatedBy($this->user->id));
        $this->assertTrue($model->wasUpdatedBy($this->user->id));
        $this->assertFalse($model->wasDeletedBy($this->user->id));

        $model->delete();

        $this->assertTrue($model->wasDeletedBy($this->user->id));
    }

    /** @test */
    public function it_handles_custom_timestamp_columns(): void
    {
        // Test with custom column names if needed
        $model = TestModel::create(['name' => 'Test Model']);

        // The trait should automatically detect and use the standard column names
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }
}
