<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Services\AppConfigService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PasscodeAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);
    }

    public function test_seeder_stores_hashed_passcode_and_epoch(): void
    {
        $config = app(AppConfigService::class);

        $this->assertNotSame('secret-pass', $config->get('student_passcode'));
        $this->assertSame(1, $config->authEpoch());
    }

    public function test_unlock_form_renders(): void
    {
        $this->get('/unlock')->assertOk()->assertSee('Passcode');
    }

    public function test_student_pages_redirect_guests_to_unlock(): void
    {
        $this->get('/directory')->assertRedirectToRoute('unlock');
        $this->get('/students/21000001')->assertRedirectToRoute('unlock');
    }

    public function test_student_pages_redirect_sessions_with_stale_epochs_to_unlock(): void
    {
        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        app(AppConfigService::class)->set('auth_epoch', '2');

        $this->withCookie('angkatan_session', $cookie->getValue())
            ->get('/students/21000001')
            ->assertRedirectToRoute('unlock');
    }

    public function test_valid_student_session_can_access_directory(): void
    {
        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->withCookie('angkatan_session', $cookie->getValue())
            ->get('/directory')
            ->assertOk();
    }

    public function test_public_scope_excludes_sensitive_student_columns(): void
    {
        $student = Student::factory()->create();

        $publicStudent = Student::public()->find($student->getKey());

        $this->assertNotNull($publicStudent);
        $this->assertArrayHasKey('name', $publicStudent->getAttributes());
        $this->assertArrayNotHasKey('address', $publicStudent->getAttributes());
        $this->assertArrayNotHasKey('whatsapp_no', $publicStudent->getAttributes());
    }

    public function test_correct_passcode_sets_http_only_cookie_and_session(): void
    {
        $response = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass']);

        $response->assertOk()->assertJsonPath('data.role', 'student');
        $cookie = $response->getCookie('angkatan_session', false);
        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());

        $this->sessionWithCookie($cookie->getValue())
            ->assertJsonPath('data.role', 'student');
    }

    public function test_wrong_passcode_returns_401(): void
    {
        $this->postJson('/api/auth/passcode', ['passcode' => 'nope'])
            ->assertUnauthorized()
            ->assertJsonPath('error.message', 'Passcode salah');
    }

    public function test_sixth_attempt_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/passcode', ['passcode' => 'nope'])->assertUnauthorized();
        }

        $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])->assertStatus(429);
    }

    public function test_epoch_bump_revokes_existing_sessions(): void
    {
        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        app(AppConfigService::class)->set('auth_epoch', '2');

        $this->sessionWithCookie($cookie->getValue())
            ->assertJsonPath('data.role', 'guest');
    }

    public function test_direct_database_epoch_update_revokes_existing_sessions(): void
    {
        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->assertSame(1, app(AppConfigService::class)->authEpoch());
        DB::table('app_config')->where('key', 'auth_epoch')->update(['value' => '2']);

        $this->sessionWithCookie($cookie->getValue())
            ->assertJsonPath('data.role', 'guest');
    }

    public function test_session_is_guest_without_cookie(): void
    {
        $this->getJson('/api/auth/session')->assertJsonPath('data.role', 'guest');
    }

    public function test_logout_clears_cookie(): void
    {
        $sessionCookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $response = $this->call(
            'DELETE',
            '/api/auth/passcode',
            [],
            ['angkatan_session' => $sessionCookie->getValue()],
            [],
            ['HTTP_ACCEPT' => 'application/json'],
        )
            ->assertOk();

        $clearedCookie = $response->getCookie('angkatan_session', false);
        $this->assertLessThan(time(), $clearedCookie->getExpiresTime());

        $this->sessionWithCookie($sessionCookie->getValue())
            ->assertJsonPath('data.role', 'guest');
    }

    protected function sessionWithCookie(string $value): TestResponse
    {
        return $this->call('GET', '/api/auth/session', [], ['angkatan_session' => $value], [], ['HTTP_ACCEPT' => 'application/json']);
    }
}
