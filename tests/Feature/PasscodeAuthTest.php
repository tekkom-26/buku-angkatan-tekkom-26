<?php

namespace Tests\Feature;

use App\Services\AppConfigService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_session_is_guest_without_cookie(): void
    {
        $this->getJson('/api/auth/session')->assertJsonPath('data.role', 'guest');
    }

    public function test_logout_clears_cookie(): void
    {
        $response = $this->deleteJson('/api/auth/passcode')->assertOk();

        $cookie = $response->getCookie('angkatan_session', false);
        $this->assertLessThan(time(), $cookie->getExpiresTime());
    }

    protected function sessionWithCookie(string $value): TestResponse
    {
        return $this->call('GET', '/api/auth/session', [], ['angkatan_session' => $value], [], ['HTTP_ACCEPT' => 'application/json']);
    }
}
