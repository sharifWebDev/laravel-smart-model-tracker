<?php

declare(strict_types=1);

namespace Sharifuddin\LaravelSmartModelTracker\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Sharifuddin\LaravelSmartModelTracker\SmartModelTracker;
use Sharifuddin\LaravelSmartModelTracker\Tests\TestCase;
use Sharifuddin\LaravelSmartModelTracker\Tests\Unit\Models\User;

final class SmartModelTrackerTest extends TestCase
{
    use RefreshDatabase;

    private SmartModelTracker $tracker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tracker = new SmartModelTracker;
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Auth::login($this->user);
    }

    /** @test */
    public function it_gets_current_user_id(): void
    {
        $userId = $this->tracker->getCurrentUserId();

        $this->assertSame($this->user->id, $userId);
    }

    /** @test */
    public function it_returns_null_when_no_user_authenticated(): void
    {
        Auth::logout();

        $userId = $this->tracker->getCurrentUserId();

        $this->assertNull($userId);
    }

    /** @test */
    public function it_gets_current_guard(): void
    {
        $guard = $this->tracker->getCurrentGuard();

        $this->assertSame('web', $guard);
    }

    /** @test */
    public function it_checks_if_tracking_is_enabled(): void
    {
        $this->assertTrue($this->tracker->isTrackingEnabled());

        Auth::logout();

        $this->assertFalse($this->tracker->isTrackingEnabled());
    }

    /** @test */
    public function it_gets_tracking_columns(): void
    {
        $columns = $this->tracker->getTrackingColumns();

        $this->assertArrayHasKey('created_at', $columns);
        $this->assertArrayHasKey('updated_at', $columns);
        $this->assertArrayHasKey('created_by', $columns);
        $this->assertArrayHasKey('updated_by', $columns);
        $this->assertArrayHasKey('deleted_by', $columns);
    }

    /** @test */
    public function it_gets_user_model_class(): void
    {
        $userModel = $this->tracker->getUserModelClass();

        $this->assertSame(User::class, $userModel);
    }

    /** @test */
    public function it_gets_current_timestamp(): void
    {
        $timestamp = $this->tracker->getCurrentTimestamp();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $timestamp);
    }

    /** @test */
    public function it_formats_timestamp(): void
    {
        $timestamp = now();
        $formatted = $this->tracker->formatTimestamp($timestamp);

        $this->assertIsString($formatted);
        $this->assertSame($timestamp->toDateTimeString(), $formatted);
    }

    /** @test */
    public function it_gets_available_guards(): void
    {
        $guards = $this->tracker->getAvailableGuards();

        $this->assertIsArray($guards);
        $this->assertContains('web', $guards);
    }

    /** @test */
    public function it_checks_guard_has_user(): void
    {
        $hasUser = $this->tracker->guardHasUser('web');

        $this->assertTrue($hasUser);
    }

    /** @test */
    public function it_gets_user_id_from_guard(): void
    {
        $userId = $this->tracker->getUserIdFromGuard('web');

        $this->assertSame($this->user->id, $userId);
    }

    /** @test */
    public function it_gets_current_user_instance(): void
    {
        $user = $this->tracker->getCurrentUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($this->user->id, $user->id);
    }

    /** @test */
    public function it_checks_tracking_column(): void
    {
        $this->assertTrue($this->tracker->isTrackingColumn('created_by'));
        $this->assertFalse($this->tracker->isTrackingColumn('nonexistent_column'));
    }

    /** @test */
    public function it_gets_tracking_column_name(): void
    {
        $columnName = $this->tracker->getTrackingColumnName('created_by');

        $this->assertSame('created_by', $columnName);
    }

    /** @test */
    public function it_gets_timestamp_columns(): void
    {
        $columns = $this->tracker->getTimestampColumns();

        $this->assertArrayHasKey('created_at', $columns);
        $this->assertArrayHasKey('updated_at', $columns);
    }

    /** @test */
    public function it_gets_user_tracking_columns(): void
    {
        $columns = $this->tracker->getUserTrackingColumns();

        $this->assertArrayHasKey('created_by', $columns);
        $this->assertArrayHasKey('updated_by', $columns);
        $this->assertArrayHasKey('deleted_by', $columns);
    }

    /** @test */
    public function it_checks_logging_enabled(): void
    {
        $enabled = $this->tracker->isLoggingEnabled();

        $this->assertTrue($enabled);
    }

    /** @test */
    public function it_gets_package_version(): void
    {
        $version = $this->tracker->getVersion();

        $this->assertSame('1.0.0', $version);
    }

    /** @test */
    public function it_gets_package_config(): void
    {
        $config = $this->tracker->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('columns', $config);
    }

    /** @test */
    public function it_checks_timestamp_tracking_enabled(): void
    {
        $enabled = $this->tracker->isTimestampTrackingEnabled();

        $this->assertTrue($enabled);
    }

    /** @test */
    public function it_checks_user_tracking_enabled(): void
    {
        $enabled = $this->tracker->isUserTrackingEnabled();

        $this->assertTrue($enabled);
    }

    /** @test */
    public function it_checks_soft_deletes_integration_enabled(): void
    {
        $enabled = $this->tracker->isSoftDeletesIntegrationEnabled();

        $this->assertTrue($enabled);
    }
}
