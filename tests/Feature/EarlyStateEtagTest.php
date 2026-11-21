<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Student;
use App\Services\AppConfigService;
use App\Services\WebContentService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EarlyStateEtagTest extends TestCase
{
    use RefreshDatabase;

    protected string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->sessionCookie = $cookie->getValue();
    }

    public function test_public_routes_return_weak_etag_and_public_edge_cache_control_headers(): void
    {
        foreach (['/', '/identity'] as $uri) {
            $response = $this->get($uri);

            $response->assertOk();

            $etag = $response->headers->get('ETag');
            $this->assertNotNull($etag);
            $this->assertMatchesRegularExpression('/^W\/"[0-9a-f]{40}"$/', $etag);

            $cacheControl = (string) $response->headers->get('Cache-Control');
            $this->assertStringContainsString('public', $cacheControl);
            $this->assertStringContainsString('max-age=0', $cacheControl);
            $this->assertStringContainsString('s-maxage=60', $cacheControl);
            $this->assertStringContainsString('stale-while-revalidate=600', $cacheControl);

            // Must emit 0 Set-Cookie headers so Vercel Edge CDN caches it (not BYPASS)
            $this->assertCount(0, $response->headers->getCookies());
        }
    }

    public function test_authenticated_student_on_public_routes_receives_private_cache_control(): void
    {
        foreach (['/', '/identity'] as $uri) {
            $response = $this->call('GET', $uri, [], ['angkatan_session' => $this->sessionCookie]);

            $response->assertOk();

            $cacheControl = (string) $response->headers->get('Cache-Control');
            $this->assertStringContainsString('private', $cacheControl);
            $this->assertStringContainsString('no-cache', $cacheControl);
            $this->assertStringNotContainsString('s-maxage', $cacheControl);
        }
    }

    public function test_protected_routes_return_weak_etag_and_private_cache_control_headers(): void
    {
        Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Adwa Rahmant',
            'class_name' => 'A',
        ]);

        foreach (['/directory', '/students/21120126120001'] as $uri) {
            $response = $this->call('GET', $uri, [], ['angkatan_session' => $this->sessionCookie]);

            $response->assertOk();

            $etag = $response->headers->get('ETag');
            $this->assertNotNull($etag);
            $this->assertMatchesRegularExpression('/^W\/"[0-9a-f]{40}"$/', $etag);

            $cacheControl = (string) $response->headers->get('Cache-Control');
            $this->assertStringContainsString('private', $cacheControl);
            $this->assertStringContainsString('no-cache', $cacheControl);
            $this->assertStringContainsString('must-revalidate', $cacheControl);
            $this->assertStringNotContainsString('s-maxage', $cacheControl);
        }
    }

    public function test_matching_if_none_match_returns_304_not_modified_with_empty_body_and_zero_db_queries(): void
    {
        Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Adwa Rahmant',
            'class_name' => 'A',
        ]);

        // Simulate fresh HTTP requests so caches (auth_epoch, web_content, directory) are warmed properly
        $this->app->forgetScopedInstances();

        $firstLanding = $this->get('/');
        $firstLanding->assertOk();
        $landingEtag = (string) $firstLanding->headers->get('ETag');

        $firstDirectory = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $firstDirectory->assertOk();
        $directoryEtag = (string) $firstDirectory->headers->get('ETag');

        $this->app->forgetScopedInstances();
        DB::enableQueryLog();
        DB::flushQueryLog();

        $cachedLanding = $this->withHeaders(['If-None-Match' => $landingEtag])->get('/');
        $cachedLanding->assertStatus(304);
        $this->assertSame('', $cachedLanding->getContent());
        $this->assertSame($landingEtag, $cachedLanding->headers->get('ETag'));

        $cachedDirectory = $this->call(
            'GET',
            '/directory',
            [],
            ['angkatan_session' => $this->sessionCookie],
            [],
            ['HTTP_IF_NONE_MATCH' => $directoryEtag],
        );
        $cachedDirectory->assertStatus(304);
        $this->assertSame('', $cachedDirectory->getContent());
        $this->assertSame($directoryEtag, $cachedDirectory->headers->get('ETag'));

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_etag_invalidates_when_student_or_app_config_changes(): void
    {
        $student = Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Adwa Rahmant',
            'class_name' => 'A',
        ]);

        $initialDirectory = $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie]);
        $initialDirectory->assertOk();
        $oldDirectoryEtag = (string) $initialDirectory->headers->get('ETag');

        // 1. Mutate Student -> must bump stateVersion and change ETag
        $student->update(['name' => 'Adwa Rahmant Updated']);

        $afterStudentUpdate = $this->call(
            'GET',
            '/directory',
            [],
            ['angkatan_session' => $this->sessionCookie],
            [],
            ['HTTP_IF_NONE_MATCH' => $oldDirectoryEtag],
        );
        $afterStudentUpdate->assertOk();
        $afterStudentUpdate->assertSee('Adwa Rahmant Updated');

        $newDirectoryEtag = (string) $afterStudentUpdate->headers->get('ETag');
        $this->assertNotSame($oldDirectoryEtag, $newDirectoryEtag);

        // 2. Mutate AppConfig / WebContent -> must bump stateVersion and change ETag
        $initialLanding = $this->get('/');
        $initialLanding->assertOk();
        $oldLandingEtag = (string) $initialLanding->headers->get('ETag');

        app(AppConfigService::class)->setCohortName('COMETS 2026');

        $afterConfigUpdate = $this->withHeaders(['If-None-Match' => $oldLandingEtag])->get('/');
        $afterConfigUpdate->assertOk();
        $afterConfigUpdate->assertSee('COMETS 2026');

        $newLandingEtag = (string) $afterConfigUpdate->headers->get('ETag');
        $this->assertNotSame($oldLandingEtag, $newLandingEtag);

        // 3. Mutate WebContentService -> must bump stateVersion and change ETag
        app(WebContentService::class)->updateIdentityContent([
            'header_banner' => [
                'title' => 'Identitas Baru ETag Test',
            ],
        ]);

        $afterWebContentUpdate = $this->withHeaders(['If-None-Match' => $newLandingEtag])->get('/');
        $afterWebContentUpdate->assertOk();
        $this->assertNotSame($newLandingEtag, $afterWebContentUpdate->headers->get('ETag'));
    }

    public function test_etag_varies_by_student_role_and_query_string(): void
    {
        $guestLanding = $this->get('/');
        $guestLanding->assertOk();
        $guestEtag = (string) $guestLanding->headers->get('ETag');

        $studentLanding = $this->call('GET', '/', [], ['angkatan_session' => $this->sessionCookie]);
        $studentLanding->assertOk();
        $studentEtag = (string) $studentLanding->headers->get('ETag');

        $this->assertNotSame($guestEtag, $studentEtag);

        $dirClassA = $this->call('GET', '/directory', ['class' => 'A'], ['angkatan_session' => $this->sessionCookie]);
        $dirClassB = $this->call('GET', '/directory', ['class' => 'B'], ['angkatan_session' => $this->sessionCookie]);

        $this->assertNotSame($dirClassA->headers->get('ETag'), $dirClassB->headers->get('ETag'));
    }
}
