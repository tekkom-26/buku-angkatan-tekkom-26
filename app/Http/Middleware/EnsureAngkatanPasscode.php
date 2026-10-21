<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the public file manager API with the shared Angkatan Passcode.
 *
 * The passcode can be supplied by the client via (checked in order):
 *   1. Header: X-Angkatan-Passcode: <passcode>
 *   2. Bearer token: Authorization: Bearer <passcode>
 *   3. Request field: passcode (query string or JSON/form body)
 *
 * NOTE: This is a lightweight guard for the public storage API, distinct
 * from the full Sanctum-based student/admin auth flow described in
 * docs/ARCHITECTURE.md. It compares against `app_config.angkatan_passcode`
 * when available, falling back to the bootstrap env value
 * (`config('angkatan.passcode')`) otherwise.
 */
class EnsureAngkatanPasscode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supplied = $this->extractPasscode($request);

        if ($supplied === null || $supplied === '') {
            return $this->unauthorized('Angkatan passcode required');
        }

        $expected = $this->resolveExpectedPasscode();

        if ($expected === null || $expected === '') {
            return $this->unauthorized('Angkatan passcode is not configured');
        }

        if (! hash_equals((string) $expected, (string) $supplied)) {
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

    /**
     * Resolve the current passcode. Prefers the DB-backed `app_config`
     * table (bcrypt hash) when the table exists; otherwise falls back to
     * the plaintext bootstrap value from the `ANGKATAN_PASSCODE` env var.
     */
    protected function resolveExpectedPasscode(): ?string
    {
        return config('angkatan.passcode');
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
