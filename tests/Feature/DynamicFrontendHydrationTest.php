<?php

namespace Tests\Feature;

use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicFrontendHydrationTest extends TestCase
{
    use RefreshDatabase;

    protected WebContentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WebContentService::class);
        $this->service->clearCache();
    }

    public function test_landing_page_renders_dynamic_content_from_service(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('COMETS 2026');

        $this->service->updateLandingContent([
            'hero' => [
                'title' => 'DYNAMIC COHORT 2026 TITLE',
                'motto' => 'DYNAMIC MOTTO 2026',
            ],
            'decoding' => [
                'wordmark' => 'DYNAMIC_WORDMARK',
            ],
            'directory_banner' => [
                'headline' => 'DYNAMIC DIRECTORY HEADLINE',
            ],
        ]);

        $updatedResponse = $this->get('/');
        $updatedResponse->assertOk();
        $updatedResponse->assertSee('DYNAMIC COHORT 2026 TITLE');
        $updatedResponse->assertSee('DYNAMIC MOTTO 2026');
        $updatedResponse->assertSee('DYNAMIC_WORDMARK');
        $updatedResponse->assertSee('DYNAMIC DIRECTORY HEADLINE');
    }

    public function test_identity_page_renders_dynamic_content_from_service(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertSee('Anatomi Identitas COMETS');

        $this->service->updateIdentityContent([
            'header_banner' => [
                'title' => 'DYNAMIC IDENTITAS ANGKATAN 2026',
            ],
            'vision_mission' => [
                'motto' => 'DYNAMIC VISION MOTTO',
            ],
        ]);

        $updatedResponse = $this->get('/identity');
        $updatedResponse->assertOk();
        $updatedResponse->assertSee('DYNAMIC IDENTITAS ANGKATAN 2026');
        $updatedResponse->assertSee('DYNAMIC VISION MOTTO');
    }

    public function test_strict_no_arbitrary_hex_in_hydrated_pages(): void
    {
        $landing = $this->get('/');
        $landing->assertOk();
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $landing->getContent());

        $identity = $this->get('/identity');
        $identity->assertOk();
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $identity->getContent());
    }
}
