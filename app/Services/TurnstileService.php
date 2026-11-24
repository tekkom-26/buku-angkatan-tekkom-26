<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TurnstileService
{
    public const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function isEnabled(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (! filled($token)) {
            return false;
        }

        $payload = [
            'secret' => (string) config('services.turnstile.secret_key'),
            'response' => (string) $token,
        ];

        if (filled($remoteIp)) {
            $payload['remoteip'] = $remoteIp;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(self::VERIFY_URL, $payload);

            return $response->successful() && $response->json('success') === true;
        } catch (Throwable $exception) {
            Log::warning('Cloudflare Turnstile verification failed', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
