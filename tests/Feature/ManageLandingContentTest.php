<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageLandingContent;
use App\Models\User;
use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageLandingContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_access_manage_landing_content_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/manage-landing-content')
            ->assertSuccessful()
            ->assertSee('Pengelolaan Konten Beranda');
    }

    public function test_form_mounts_with_default_landing_content(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageLandingContent::class)
            ->assertSchemaStateSet([
                'hero.title' => config('content.landing.hero.title'),
                'hero.motto' => config('content.landing.hero.motto'),
                'decoding.wordmark' => config('content.landing.decoding.wordmark'),
                'directory_banner.headline' => config('content.landing.directory_banner.headline'),
            ]);
    }

    public function test_admin_can_save_updated_landing_content_and_clears_cache(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageLandingContent::class)
            ->fillForm([
                'hero' => [
                    'title' => 'SUPER COHORT 2026',
                    'motto' => 'BERSAMA KITA BISA',
                    'narrative' => 'Deskripsi baru yang keren...',
                    'directory_url' => '/directory',
                    'identity_url' => '/identity',
                ],
                'decoding' => [
                    'wordmark' => 'TEKKOM 26',
                ],
                'directory_banner' => [
                    'headline' => 'Direktori Mahasiswa 2026',
                    'description' => 'Akses seluruh profil teman seangkatan.',
                    'cta_label' => 'Buka Sekarang',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konten Beranda Berhasil Diperbarui');

        $service = app(WebContentService::class);
        $content = $service->getLandingContent();

        $this->assertEquals('SUPER COHORT 2026', $content['hero']['title']);
        $this->assertEquals('BERSAMA KITA BISA', $content['hero']['motto']);
        $this->assertEquals('TEKKOM 26', $content['decoding']['wordmark']);
        $this->assertEquals('Direktori Mahasiswa 2026', $content['directory_banner']['headline']);
    }
}
