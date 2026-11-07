<?php

namespace Tests\Feature;

use App\Models\AppConfig;
use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WebContentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WebContentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WebContentService;
        $this->service->clearCache();
    }

    public function test_get_landing_content_returns_defaults_when_database_is_empty(): void
    {
        $content = $this->service->getLandingContent();

        $this->assertIsArray($content);
        $this->assertEquals(config('content.landing.hero.title'), $content['hero']['title']);
        $this->assertEquals(config('content.landing.decoding.wordmark'), $content['decoding']['wordmark']);
        $this->assertEquals(config('content.landing.directory_banner.headline'), $content['directory_banner']['headline']);
    }

    public function test_get_identity_content_returns_defaults_when_database_is_empty(): void
    {
        $content = $this->service->getIdentityContent();

        $this->assertIsArray($content);
        $this->assertEquals(config('content.identity.header_banner.title'), $content['header_banner']['title']);
        $this->assertEquals(config('content.identity.vision_mission.motto'), $content['vision_mission']['motto']);
        $this->assertCount(4, $content['color_pillars']['pillars']);
        $this->assertCount(4, $content['logo_symbolism']['slides']);
        $this->assertCount(4, $content['mascot']['slides']);
    }

    public function test_update_landing_content_persists_to_database_and_clears_cache(): void
    {
        $updatedData = [
            'hero' => [
                'title' => 'NEW HERO TITLE 2026',
                'motto' => 'NEW MOTTO',
            ],
        ];

        $this->service->updateLandingContent($updatedData);

        $this->assertDatabaseHas('app_config', [
            'key' => WebContentService::KEY_LANDING,
        ]);

        $content = $this->service->getLandingContent();
        $this->assertEquals('NEW HERO TITLE 2026', $content['hero']['title']);
        $this->assertEquals('NEW MOTTO', $content['hero']['motto']);
    }

    public function test_update_identity_content_persists_to_database_and_clears_cache(): void
    {
        $updatedData = [
            'header_banner' => [
                'title' => 'FILOSOFI TERBARU 2026',
            ],
        ];

        $this->service->updateIdentityContent($updatedData);

        $this->assertDatabaseHas('app_config', [
            'key' => WebContentService::KEY_IDENTITY,
        ]);

        $content = $this->service->getIdentityContent();
        $this->assertEquals('FILOSOFI TERBARU 2026', $content['header_banner']['title']);
    }

    public function test_partial_update_merges_gracefully_with_defaults(): void
    {
        // Admin updates ONLY hero title, omitting narrative, directory_url, decoding, etc.
        $this->service->updateLandingContent([
            'hero' => [
                'title' => 'ONLY UPDATED HERO TITLE',
            ],
        ]);

        $content = $this->service->getLandingContent();

        // Updated field is applied
        $this->assertEquals('ONLY UPDATED HERO TITLE', $content['hero']['title']);

        // Untouched sub-fields in 'hero' cleanly fall back to defaults
        $this->assertEquals(config('content.landing.hero.motto'), $content['hero']['motto']);
        $this->assertEquals(config('content.landing.hero.narrative'), $content['hero']['narrative']);

        // Untouched sections cleanly fall back to defaults
        $this->assertEquals(config('content.landing.decoding.wordmark'), $content['decoding']['wordmark']);
        $this->assertEquals(config('content.landing.directory_banner.headline'), $content['directory_banner']['headline']);
    }

    public function test_caching_layer_prevents_unnecessary_database_queries(): void
    {
        // 1. Initial call warms up cache with default
        $firstCall = $this->service->getLandingContent();
        $this->assertEquals(config('content.landing.hero.title'), $firstCall['hero']['title']);

        // 2. Direct modification in DB without invalidating cache
        AppConfig::query()->updateOrCreate(
            ['key' => WebContentService::KEY_LANDING],
            ['value' => json_encode(['hero' => ['title' => 'SNEAKY DB UPDATE']])]
        );

        // 3. Second call must return cached data (not 'SNEAKY DB UPDATE')
        $secondCall = $this->service->getLandingContent();
        $this->assertEquals(config('content.landing.hero.title'), $secondCall['hero']['title']);

        // 4. Invalidate cache via clearCache()
        $this->service->clearCache('landing');

        // 5. Third call must now reflect the database value
        $thirdCall = $this->service->getLandingContent();
        $this->assertEquals('SNEAKY DB UPDATE', $thirdCall['hero']['title']);
    }

    public function test_failsafe_fallback_when_database_json_is_corrupt(): void
    {
        // Corrupt JSON string in DB
        AppConfig::query()->updateOrCreate(
            ['key' => WebContentService::KEY_LANDING],
            ['value' => '{"corrupted_json: true, ...}']
        );

        $this->service->clearCache('landing');

        $content = $this->service->getLandingContent();

        // Must not crash and must cleanly return default config
        $this->assertIsArray($content);
        $this->assertEquals(config('content.landing.hero.title'), $content['hero']['title']);
        $this->assertEquals(config('content.landing.decoding.wordmark'), $content['decoding']['wordmark']);
    }
}
