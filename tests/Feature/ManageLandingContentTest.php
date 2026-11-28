<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageLandingContent;
use App\Models\StorageFile;
use App\Models\User;
use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_decoding_logo_image_to_r2_from_landing_cms(): void
    {
        config(['filesystems.disks.r2.url' => 'https://cdn.subekti.web.id']);
        Storage::fake('local');
        Storage::fake('r2', ['url' => 'https://cdn.subekti.web.id']);

        $logoFile = UploadedFile::fake()->createWithContent(
            'comets-logo.png',
            (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='),
        );

        Livewire::actingAs($this->admin)
            ->test(ManageLandingContent::class)
            ->fillForm([
                'decoding' => [
                    'wordmark' => 'COMETS',
                    'logo_upload' => $logoFile,
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konten Beranda Berhasil Diperbarui');

        $content = app(WebContentService::class)->getLandingContent();
        $logoUrl = $content['decoding']['logo_placeholder'];

        $this->assertArrayNotHasKey('logo_upload', $content['decoding']);
        $this->assertMatchesRegularExpression('#^https://cdn\.subekti\.web\.id/content/landing/decoding-logo-[a-z0-9]{8}\.png\?v=\d+$#', $logoUrl);
        $this->assertSame(1, StorageFile::query()->where('disk', 'r2')->where('visibility', StorageFile::VISIBILITY_PUBLIC)->count());

        $this->get('/')
            ->assertOk()
            ->assertSee($logoUrl, false);
    }
}
