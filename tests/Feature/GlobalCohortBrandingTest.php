<?php

namespace Tests\Feature;

use App\Filament\Pages\ConfigManagement;
use App\Models\User;
use App\Services\AppConfigService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalCohortBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected AppConfigService $configService;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configService = app(AppConfigService::class);
        $this->configService->forget(AppConfigService::KEY_COHORT_NAME);
        $this->admin = User::factory()->create();
    }

    public function test_default_cohort_name_renders_globally_when_no_custom_name_is_set(): void
    {
        $this->assertEquals('TEKNIK KOMPUTER 2026', $this->configService->cohortName());

        // 1. Landing Page title
        $landing = $this->get('/');
        $landing->assertOk();
        $landing->assertSee('<title>TEKNIK KOMPUTER 2026 — Buku Angkatan</title>', false);

        // 2. Identity Page title
        $identity = $this->get('/identity');
        $identity->assertOk();
        $identity->assertSee('<title>Identitas Angkatan — TEKNIK KOMPUTER 2026</title>', false);
    }

    public function test_admin_can_update_cohort_name_via_filament_config_management_and_propagates_globally(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ConfigManagement::class)
            ->assertSchemaStateSet([
                'cohort_name' => 'TEKNIK KOMPUTER 2026',
            ])
            ->fillForm([
                'cohort_name' => 'KONTINGEN DUTA 2026',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konfigurasi Berhasil Diperbarui');

        $this->assertDatabaseHas('app_config', [
            'key' => AppConfigService::KEY_COHORT_NAME,
            'value' => 'KONTINGEN DUTA 2026',
        ]);

        $this->assertEquals('KONTINGEN DUTA 2026', $this->configService->cohortName());

        // Global propagation check
        $landing = $this->get('/');
        $landing->assertOk();
        $landing->assertSee('<title>KONTINGEN DUTA 2026 — Buku Angkatan</title>', false);

        $identity = $this->get('/identity');
        $identity->assertOk();
        $identity->assertSee('<title>Identitas Angkatan — KONTINGEN DUTA 2026</title>', false);
    }

    public function test_public_pages_do_not_contain_hardcoded_ascent_wordmark(): void
    {
        $landing = $this->get('/');
        $landing->assertOk();
        $landing->assertDontSee('ASCENT');

        $identity = $this->get('/identity');
        $identity->assertOk();
        $identity->assertDontSee('ASCENT');
    }
}
