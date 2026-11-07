<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageIdentityContent;
use App\Models\User;
use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageIdentityContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_access_manage_identity_content_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/manage-identity-content')
            ->assertSuccessful()
            ->assertSee('Pengelolaan Konten Filosofi &amp; Identitas', false);
    }

    public function test_form_mounts_with_default_identity_content(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageIdentityContent::class)
            ->assertSchemaStateSet([
                'header_banner.title' => config('content.identity.header_banner.title'),
                'vision_mission.motto' => config('content.identity.vision_mission.motto'),
            ]);
    }

    public function test_admin_can_save_updated_identity_content_and_clears_cache(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageIdentityContent::class)
            ->fillForm([
                'header_banner' => [
                    'title' => 'Identitas Baru Angkatan 2026',
                    'subhead' => 'Dokumentasi terperinci mengenai filosofi angkatan.',
                ],
                'vision_mission' => [
                    'motto' => 'Integrity and Honor',
                    'vision_body' => 'Menjadi yang terbaik dalam inovasi teknologi.',
                    'missions' => [
                        [
                            'number' => '01.',
                            'title' => 'Misi Pertama Terintegrasi',
                            'description' => 'Penjelasan lengkap misi satu.',
                        ],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konten Identitas Berhasil Diperbarui');

        $service = app(WebContentService::class);
        $content = $service->getIdentityContent();

        $this->assertEquals('Identitas Baru Angkatan 2026', $content['header_banner']['title']);
        $this->assertEquals('Integrity and Honor', $content['vision_mission']['motto']);
        $this->assertCount(1, $content['vision_mission']['missions']);
        $this->assertEquals('Misi Pertama Terintegrasi', $content['vision_mission']['missions'][0]['title']);
    }
}
