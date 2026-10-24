<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;

class StudentSessionService
{
    public const COOKIE_NAME = 'angkatan_session';

    public function __construct(protected AppConfigService $config) {}

    public function issueToken(int $epoch, Carbon $expiresAt): string
    {
        DB::table('student_sessions')->where('expires_at', '<=', now())->delete();

        $sessionId = (string) Str::uuid();

        DB::table('student_sessions')->insert([
            'id' => $sessionId,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Crypt::encryptString(json_encode([
            'role' => 'student',
            'epoch' => $epoch,
            'expires_at' => $expiresAt->getTimestamp(),
            'session_id' => $sessionId,
        ], JSON_THROW_ON_ERROR));
    }

    public function resolveRole(?string $token): string
    {
        $payload = $this->decodeToken($token);

        if ($payload === null
            || ($payload['role'] ?? null) !== 'student'
            || ! is_int($payload['expires_at'] ?? null)
            || $payload['expires_at'] < Carbon::now()->getTimestamp()
            || ($payload['epoch'] ?? null) !== $this->config->authEpoch()
            || ! $this->hasActiveSession($payload['session_id'] ?? null)) {
            return 'guest';
        }

        return 'student';
    }

    public function resolveRequestRole(Request $request): string
    {
        return $this->resolveRole($request->cookie(self::COOKIE_NAME));
    }

    public function revokeRequestSession(Request $request): void
    {
        $payload = $this->decodeToken($request->cookie(self::COOKIE_NAME));
        $sessionId = $payload['session_id'] ?? null;

        if (($payload['role'] ?? null) !== 'student' || ! $this->isSessionId($sessionId)) {
            return;
        }

        DB::table('student_sessions')->where('id', $sessionId)->delete();
    }

    /** @return array<string, mixed>|null */
    private function decodeToken(?string $token): ?array
    {
        if (! is_string($token) || $token === '') {
            return null;
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|JsonException) {
            return null;
        }

        return is_array($payload) ? $payload : null;
    }

    private function hasActiveSession(mixed $sessionId): bool
    {
        if (! $this->isSessionId($sessionId)) {
            return false;
        }

        return DB::table('student_sessions')
            ->where('id', $sessionId)
            ->where('expires_at', '>', now())
            ->exists();
    }

    private function isSessionId(mixed $sessionId): bool
    {
        return is_string($sessionId)
            && preg_match('/^[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $sessionId) === 1;
    }
}
