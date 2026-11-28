<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StorageFile;
use App\Models\User;
use App\Services\AppConfigService;
use App\Services\R2FileManagerService;
use Database\Seeders\AuthSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class AdminAndStorageSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'initial-pass']);
        $this->seed(AuthSeeder::class);
    }

    public function test_unauthenticated_users_cannot_access_any_admin_panel_routes(): void
    {
        $adminRoutes = [
            '/admin',
            '/admin/students',
            '/admin/manage-landing-content',
            '/admin/manage-identity-content',
            '/admin/config-management',
            '/admin/file-manager',
        ];

        foreach ($adminRoutes as $route) {
            $this->get($route)->assertRedirect('/admin/login');
        }
    }

    public function test_admin_allowlist_restricts_panel_access_to_configured_emails(): void
    {
        config(['angkatan.admin_allowed_emails' => ['adwarahmant@gmail.com', 'teknikkomp26@gmail.com']]);

        $allowedAdmin = User::factory()->create(['email' => 'adwarahmant@gmail.com']);
        $blockedUser = User::factory()->create(['email' => 'intruder@gmail.com']);

        $this->actingAs($allowedAdmin)
            ->get('/admin')
            ->assertOk();

        $this->actingAs($blockedUser)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_dummy_example_email_is_blocked_from_panel_in_production_even_without_allowlist(): void
    {
        config(['angkatan.admin_allowed_emails' => []]);
        $this->app->detectEnvironment(static fn (): string => 'production');

        $panel = Filament::getPanel('admin');

        $dummyUser = User::factory()->make(['email' => 'test@example.com']);
        $realAdmin = User::factory()->make(['email' => 'adwarahmant@gmail.com']);

        $this->assertFalse($dummyUser->canAccessPanel($panel));
        $this->assertTrue($realAdmin->canAccessPanel($panel));
    }

    public function test_security_headers_are_applied_to_public_and_admin_responses(): void
    {
        $publicResponse = $this->get('/');
        $publicResponse->assertOk();
        $publicResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $publicResponse->assertHeader('X-Frame-Options', 'DENY');
        $publicResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $publicResponse->assertHeaderMissing('X-Powered-By');

        $adminLoginResponse = $this->get('/admin/login');
        $adminLoginResponse->assertOk();
        $adminLoginResponse->assertHeader('X-Frame-Options', 'DENY');
        $adminLoginResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $adminLoginResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $adminLoginResponse->assertHeaderMissing('X-Powered-By');
    }

    public function test_public_storage_api_cannot_modify_or_delete_student_photos_or_cms_images(): void
    {
        Storage::fake('r2');
        Storage::disk('r2')->put('students/21120126140134/formal.jpg', 'student-photo');
        Storage::disk('r2')->put('content/identity/logo/lingkaran.png', 'cms-logo');
        Storage::disk('r2')->put('public/album/safe.jpg', 'public-photo');

        $studentPhoto = StorageFile::factory()->public()->create([
            'disk' => 'r2',
            'path' => 'students/21120126140134/formal.jpg',
            'prefix' => 'students',
        ]);
        $cmsImage = StorageFile::factory()->public()->create([
            'disk' => 'r2',
            'path' => 'content/identity/logo/lingkaran.png',
            'prefix' => 'content',
        ]);
        $publicPhoto = StorageFile::factory()->public()->create([
            'disk' => 'r2',
            'path' => 'public/album/safe.jpg',
            'prefix' => 'album',
        ]);

        $headers = ['X-Angkatan-Passcode' => 'initial-pass'];

        $this->withHeaders($headers)
            ->deleteJson("/api/storage/public/files/{$studentPhoto->id}")
            ->assertNotFound();

        $this->withHeaders($headers)
            ->patchJson("/api/storage/public/files/{$cmsImage->id}", ['alias' => 'hijacked'])
            ->assertNotFound();

        Storage::disk('r2')->assertExists('students/21120126140134/formal.jpg');
        Storage::disk('r2')->assertExists('content/identity/logo/lingkaran.png');

        // File genuinely under public/ prefix remains deletable
        $this->withHeaders($headers)
            ->deleteJson("/api/storage/public/files/{$publicPhoto->id}")
            ->assertOk();

        Storage::disk('r2')->assertMissing('public/album/safe.jpg');
    }

    public function test_public_storage_api_rejects_path_traversal_and_executable_or_svg_uploads(): void
    {
        Storage::fake('r2');
        $headers = ['X-Angkatan-Passcode' => 'initial-pass'];

        $this->withHeaders($headers)
            ->getJson('/api/storage/public?path=../students')
            ->assertUnprocessable();

        $this->withHeaders($headers)
            ->postJson('/api/storage/public/directories', ['path' => '../students/hack'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['path']);

        $svgPayload = UploadedFile::fake()->createWithContent(
            'xss.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>',
        );

        $this->withHeaders($headers)
            ->post('/api/storage/public/files', [
                'file' => $svgPayload,
                'path' => 'uploads/xss.svg',
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);

        $htmlPayload = UploadedFile::fake()->createWithContent('phish.html', '<html><body>x</body></html>');

        $this->withHeaders($headers)
            ->post('/api/storage/public/files', [
                'file' => $htmlPayload,
                'path' => 'uploads/phish.html',
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);

        $this->expectException(InvalidArgumentException::class);
        app(R2FileManagerService::class)->createDirectory('public/../escape');
    }

    public function test_public_storage_passcode_middleware_respects_rotated_db_passcode(): void
    {
        Storage::fake('r2');

        app(AppConfigService::class)->set(
            AppConfigService::KEY_STUDENT_PASSCODE,
            Hash::make('rotated-secret-2026'),
        );

        // Old env passcode must no longer work after rotation
        $this->withHeaders(['X-Angkatan-Passcode' => 'initial-pass'])
            ->getJson('/api/storage/public')
            ->assertUnauthorized();

        // Rotated DB passcode succeeds
        $this->withHeaders(['X-Angkatan-Passcode' => 'rotated-secret-2026'])
            ->getJson('/api/storage/public')
            ->assertOk();
    }
}
