<?php

namespace App\Http\Middleware;

use App\Services\AppConfigService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Guards the public file manager API with the shared Angkatan Passcode.
 *
 * The passcode can be supplied by the client via (checked in order):
 *   1. Header: X-Angkatan-Passcode: <passcode>
 *   2. Bearer token: Authorization: Bearer <passcode>
 *   3. Request field: passcode (query string or JSON/form body)
 *
 * Checks against the DB-backed bcrypt hash (`app_config.student_passcode`) first
 * so passcode rotations from the admin panel take effect immediately, falling back
 * to `config('angkatan.passcode')` only when the DB record is not yet seeded.
 */
class EnsureAngkatanPasscode
{
    public function __construct(protected AppConfigService $config) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supplied = $this->extractPasscode($request);

        if ($supplied === null || $supplied === '') {
            return $this->unauthorized('Angkatan passcode required');
        }

        if (! $this->verifyPasscode($supplied)) {
            return $this->unauthorized('Invalid angkatan passcode');
        }

        return $next($request);
    }

    protected function extractPasscode(Request $request): ?string
    {
        if ($header = $request->header('X-Angkatan-Passcode')) {
            return $header;
        }

        if ($bearer = $request->bearerToken()) {
            return $bearer;
        }

        $field = $request->input('passcode');

        return is_string($field) ? $field : null;
    }

    protected function verifyPasscode(string $supplied): bool
    {
        try {
            $dbHash = $this->config->get(AppConfigService::KEY_STUDENT_PASSCODE);
        } catch (Throwable) {
            $dbHash = null;
        }

        if (is_string($dbHash) && $dbHash !== '') {
            return Hash::check($supplied, $dbHash);
        }

        $fallback = config('angkatan.passcode');

        if (! is_string($fallback) || $fallback === '') {
            return false;
        }

        return hash_equals($fallback, $supplied);
    }

    protected function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'UNAUTHORIZED',
                'message' => $message,
            ],
        ], 401);
    }
}
