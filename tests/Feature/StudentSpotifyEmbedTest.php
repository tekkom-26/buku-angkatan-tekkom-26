<?php

namespace Tests\Feature;

use App\Models\Student;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSpotifyEmbedTest extends TestCase
{
    use RefreshDatabase;

    private string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->sessionCookie = $cookie->getValue();
    }

    public function test_renders_track_embed_for_track_url(): void
    {
        Student::factory()->create([
            'nim' => '21120126120001',
            'spotify_song_url' => 'https://open.spotify.com/track/7yCF65vpc7sUwPjftI0iFb?si=06036673457c41ad',
        ]);

        $response = $this->call('GET', '/students/21120126120001', [], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('https://open.spotify.com/embed/track/7yCF65vpc7sUwPjftI0iFb?utm_source=generator', false);
        $response->assertSee('Lagu Favorit di Spotify');
    }

    public function test_renders_playlist_embed_for_playlist_url(): void
    {
        Student::factory()->create([
            'nim' => '21120126120002',
            'spotify_song_url' => 'https://open.spotify.com/playlist/5c58pkhoT3bWmAcpc6iuWU?si=ZHtVIAfSQTOT57teZ50qfQ',
        ]);

        $response = $this->call('GET', '/students/21120126120002', [], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('https://open.spotify.com/embed/playlist/5c58pkhoT3bWmAcpc6iuWU?utm_source=generator', false);
        $response->assertSee('Playlist Favorit di Spotify');
    }

    public function test_renders_fallback_when_spotify_url_is_invalid(): void
    {
        Student::factory()->create([
            'nim' => '21120126120003',
            'spotify_song_url' => 'https://example.com/not-spotify',
        ]);

        $response = $this->call('GET', '/students/21120126120003', [], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Spotify belum ditambahkan.');
    }
}
