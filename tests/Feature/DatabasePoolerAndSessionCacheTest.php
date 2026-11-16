<?php

namespace Tests\Feature;

use App\Services\AppConfigService;
use App\Services\StudentSessionService;
use App\Services\WebContentService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PDO;
use Tests\TestCase;

class DatabasePoolerAndSessionCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);
    }

    public function test_pgsql_connection_enables_emulate_prepares_and_timeout_for_supabase_pooler(): void
    {
        $options = config('database.connections.pgsql.options');

        $this->assertIsArray($options);

        if (extension_loaded('pdo_pgsql')) {
            $this->assertTrue($options[PDO::ATTR_EMULATE_PREPARES] ?? false);
            $this->assertSame(5, $options[PDO::ATTR_TIMEOUT] ?? null);
        }
    }

    public function test_auth_epoch_and_cohort_name_are_cached_and_memoized_with_zero_repeated_queries(): void
    {
        $configService = app(AppConfigService::class);

        // Warm up cache
        $this->assertSame(1, $configService->authEpoch());
        $this->assertNotEmpty($configService->cohortName());

        DB::enableQueryLog();
        DB::flushQueryLog();

        // Repeated calls within and across services should execute 0 SQL queries
        for ($i = 0; $i < 15; $i++) {
            $this->assertSame(1, $configService->authEpoch());
            $this->assertNotEmpty($configService->cohortName());
        }

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_student_session_validation_executes_zero_database_queries_when_cached(): void
    {
        $response = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass']);
        $cookie = $response->getCookie(StudentSessionService::COOKIE_NAME, false);
        $this->assertNotNull($cookie);

        $sessionService = app(StudentSessionService::class);
        $token = $cookie->getValue();

        // Warm up authEpoch cache
        $this->assertSame('student', $sessionService->resolveRole($token));

        DB::enableQueryLog();
        DB::flushQueryLog();

        // Subsequent session validations must hit cache + memo with 0 SQL queries
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame('student', $sessionService->resolveRole($token));
        }

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_landing_and_identity_pages_execute_zero_database_queries_once_warm(): void
    {
        // First load warms up AppConfig and WebContent caches
        $this->get('/')->assertOk();
        $this->get('/identity')->assertOk();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->get('/')->assertOk();
        $this->get('/identity')->assertOk();

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_web_content_service_memoizes_repeated_calls_in_same_request(): void
    {
        $webContent = app(WebContentService::class);
        $webContent->getLandingContent();
        $webContent->getIdentityContent();

        DB::enableQueryLog();
        DB::flushQueryLog();

        for ($i = 0; $i < 5; $i++) {
            $this->assertIsArray($webContent->getLandingContent());
            $this->assertIsArray($webContent->getIdentityContent());
        }

        $this->assertCount(0, DB::getQueryLog());
    }
}
