<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageIdentityContent;
use App\Models\StorageFile;
use App\Models\User;
use App\Services\ContentImageService;
use App\Services\WebContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_logo_mascot_and_banner_images_to_r2_and_render_on_identity_page(): void
    {
        config(['filesystems.disks.r2.url' => 'https://cdn.subekti.web.id']);
        Storage::fake('local');
        Storage::fake('r2', ['url' => 'https://cdn.subekti.web.id']);

        $bannerFile = $this->fakeImage('banner.jpg');
        $logoFile = $this->fakeImage('lingkaran.png');
        $mascotFile = $this->fakeImage('bee-ner-eye.webp');

        Livewire::actingAs($this->admin)
            ->test(ManageIdentityContent::class)
            ->fillForm([
                'header_banner' => [
                    'title' => 'Anatomi Identitas COMETS',
                    'subhead' => 'Dokumentasi filosofi resmi angkatan.',
                    'banner_upload' => $bannerFile,
                ],
                'logo_symbolism' => [
                    'eyebrow' => 'Anatomi Lambang',
                    'title' => 'Bedah Simbolisme',
                    'subtitle' => 'Elemen Lambang COMETS',
                    'description' => 'Penjelasan elemen logo.',
                    'slides' => [
                        [
                            'number' => '01',
                            'eyebrow' => 'Kesetaraan',
                            'title' => 'Lingkaran Pelindung',
                            'detail' => 'Saling Melindungi',
                            'part' => 'Lingkaran',
                            'image_upload' => [$logoFile],
                            'description' => 'Lingkaran utuh melambangkan kesetaraan.',
                        ],
                    ],
                ],
                'mascot' => [
                    'eyebrow' => 'Karakter Maskot',
                    'title' => 'Bedah Karakter',
                    'subtitle' => '& Anatomi Bee-ner',
                    'description' => 'Penjelasan atribut maskot Bee-ner.',
                    'slides' => [
                        [
                            'number' => '01',
                            'eyebrow' => 'Potensi Diri',
                            'title' => 'Mata Hexagon',
                            'detail' => 'Visi Hexagon',
                            'part' => 'Mata Hexagon',
                            'image_upload' => [$mascotFile],
                            'description' => 'Mata berbentuk segi enam yang menyala.',
                        ],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Konten Identitas Berhasil Diperbarui');

        $content = app(WebContentService::class)->getIdentityContent();

        $bannerUrl = $content['header_banner']['banner_placeholder'];
        $logoUrl = $content['logo_symbolism']['slides'][0]['image'];
        $mascotUrl = $content['mascot']['slides'][0]['image'];

        $this->assertArrayNotHasKey('banner_upload', $content['header_banner']);
        $this->assertArrayNotHasKey('image_upload', $content['logo_symbolism']['slides'][0]);
        $this->assertArrayNotHasKey('image_upload', $content['mascot']['slides'][0]);

        $this->assertMatchesRegularExpression('#^https://cdn\.subekti\.web\.id/content/identity/header-banner-[a-z0-9]{8}\.jpg\?v=\d+$#', $bannerUrl);
        $this->assertMatchesRegularExpression('#^https://cdn\.subekti\.web\.id/content/identity/logo/lingkaran-[a-z0-9]{8}\.png\?v=\d+$#', $logoUrl);
        $this->assertMatchesRegularExpression('#^https://cdn\.subekti\.web\.id/content/identity/mascot/mata-hexagon-[a-z0-9]{8}\.webp\?v=\d+$#', $mascotUrl);

        $this->assertSame(3, StorageFile::query()->where('disk', 'r2')->where('visibility', StorageFile::VISIBILITY_PUBLIC)->count());
        $this->assertSame([], Storage::disk('local')->files('tmp-uploads'));

        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertSee($bannerUrl, false);
        $response->assertSee($logoUrl, false);
        $response->assertSee($mascotUrl, false);
        $response->assertSee('Lingkaran Pelindung');
        $response->assertSee('Mata Hexagon');
    }

    public function test_replacing_slide_image_cleans_up_previous_r2_object_without_deleting_unrelated_files(): void
    {
        config(['filesystems.disks.r2.url' => 'https://cdn.subekti.web.id']);
        Storage::fake('local');
        Storage::fake('r2', ['url' => 'https://cdn.subekti.web.id']);

        Storage::disk('r2')->put('content/identity/logo/old-lingkaran.png', 'old-bytes');
        $oldRecord = StorageFile::factory()->public()->create([
            'disk' => 'r2',
            'path' => 'content/identity/logo/old-lingkaran.png',
            'prefix' => 'content',
        ]);

        $staged = Storage::disk('local')->putFileAs(
            'tmp-uploads',
            $this->fakeImage('new-lingkaran.png'),
            'new-lingkaran.png',
        );

        $result = app(ContentImageService::class)->processIdentityFormData([
            'logo_symbolism' => [
                'slides' => [
                    [
                        'part' => 'Lingkaran',
                        'image' => 'https://cdn.subekti.web.id/content/identity/logo/old-lingkaran.png?v=123',
                        'image_upload' => $staged,
                    ],
                ],
            ],
        ]);

        Storage::disk('r2')->assertMissing('content/identity/logo/old-lingkaran.png');
        $this->assertDatabaseMissing('storage_files', ['id' => $oldRecord->id]);
        $this->assertStringStartsWith('https://cdn.subekti.web.id/content/identity/logo/lingkaran-', $result['logo_symbolism']['slides'][0]['image']);
    }

    public function test_untouched_legacy_four_slide_data_falls_back_to_comets_beener_defaults_until_edited(): void
    {
        $service = app(WebContentService::class);

        $service->updateIdentityContent([
            'logo_symbolism' => [
                'slides' => [
                    ['number' => '01', 'eyebrow' => 'OLD', 'title' => 'Old 1', 'detail' => 'D', 'part' => 'WINGS', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '02', 'eyebrow' => 'OLD', 'title' => 'Old 2', 'detail' => 'D', 'part' => 'NUMERAL 26', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '03', 'eyebrow' => 'OLD', 'title' => 'Old 3', 'detail' => 'D', 'part' => 'CIRCUIT', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '04', 'eyebrow' => 'OLD', 'title' => 'Old 4', 'detail' => 'D', 'part' => 'FULL EMBLEM', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                ],
            ],
            'mascot' => [
                'slides' => [
                    ['number' => '01', 'eyebrow' => 'OLD', 'title' => 'Gading Komputasi', 'detail' => 'D', 'part' => 'TUSK', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '02', 'eyebrow' => 'OLD', 'title' => 'Belalai Ramah', 'detail' => 'D', 'part' => 'TRUNK', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '03', 'eyebrow' => 'OLD', 'title' => 'Almamater', 'detail' => 'D', 'part' => 'JACKET', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                    ['number' => '04', 'eyebrow' => 'OLD', 'title' => 'Pose Penuh', 'detail' => 'D', 'part' => 'FULL BODY', 'image' => '/assets/Placeholder.png', 'description' => 'Desc'],
                ],
            ],
        ]);

        // Public page still renders COMETS 2026 & Bee-ner 5-slide defaults
        $this->get('/identity')
            ->assertOk()
            ->assertSee('Lingkaran — Kesetaraan Tanpa Awal &amp; Akhir', false)
            ->assertSee('Strip Hitam-Emas — Logika Biner 0 &amp; 1', false)
            ->assertDontSee('Gading Komputasi');

        // Admin form also pre-fills the 5 COMETS / Bee-ner slides ready for editing & upload
        Livewire::actingAs($this->admin)
            ->test(ManageIdentityContent::class)
            ->assertSchemaStateSet(function (array $state): array {
                $this->assertCount(5, $state['logo_symbolism']['slides']);
                $this->assertCount(5, $state['mascot']['slides']);

                return [];
            });
    }

    public function test_upload_failure_when_r2_public_domain_missing_notifies_and_does_not_overwrite_content(): void
    {
        config(['filesystems.disks.r2.url' => null]);
        Storage::fake('local');

        $logoFile = $this->fakeImage('fail-logo.png');

        Livewire::actingAs($this->admin)
            ->test(ManageIdentityContent::class)
            ->fillForm([
                'header_banner' => [
                    'title' => 'Judul Gagal Simpan',
                    'subhead' => 'Subhead baru.',
                ],
                'logo_symbolism' => [
                    'slides' => [
                        [
                            'number' => '01',
                            'eyebrow' => 'Test',
                            'title' => 'Test Logo',
                            'detail' => 'Detail',
                            'part' => 'Logo',
                            'image_upload' => [$logoFile],
                            'description' => 'Desc',
                        ],
                    ],
                ],
            ])
            ->call('save')
            ->assertNotified('Upload gambar gagal');

        $this->assertNotEquals(
            'Judul Gagal Simpan',
            app(WebContentService::class)->getIdentityContent()['header_banner']['title'],
        );
    }

    public function test_preview_url_hides_placeholder_logo_on_photo_slots_until_real_image_uploaded(): void
    {
        $service = app(ContentImageService::class);

        $this->assertNull($service->previewUrl('/assets/Placeholder.png', ignorePlaceholder: true));
        $this->assertNotNull($service->previewUrl('/assets/Placeholder.png', ignorePlaceholder: false));
        $this->assertSame(
            'https://cdn.subekti.web.id/content/landing/moments/foto.jpg',
            $service->previewUrl('https://cdn.subekti.web.id/content/landing/moments/foto.jpg', ignorePlaceholder: true),
        );
    }

    /**
     * Buat file gambar valid (PNG / JPG / WEBP) tanpa bergantung pada ekstensi GD.
     */
    protected function fakeImage(string $filename): UploadedFile
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $bytes = match ($extension) {
            'jpg', 'jpeg' => (string) base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='),
            'webp' => (string) base64_decode('UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA='),
            default => (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='),
        };

        return UploadedFile::fake()->createWithContent($filename, $bytes);
    }
}
