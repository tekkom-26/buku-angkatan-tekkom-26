<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Requests\VerifyPasscodeRequest;
use App\Services\AppConfigService;
use App\Services\StudentSessionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PasscodeController extends Controller
{
    use ApiResponses;

    public const COOKIE_NAME = StudentSessionService::COOKIE_NAME;

    public const SESSION_TTL_SECONDS = 604800; // 7 days

    protected const MAX_ATTEMPTS = 5;

    protected const DECAY_SECONDS = 900; // 15 minutes

    public function __construct(
        protected AppConfigService $config,
        protected StudentSessionService $sessions,
    ) {}

    /**
     * GET /unlock
     */
    public function showForm(): View
    {
        return view('unlock');
    }

    /**
     * POST /api/auth/passcode
     */
    public function verify(VerifyPasscodeRequest $request): JsonResponse
    {
        $limiterKey = 'passcode:'.$request->ip();

        if (RateLimiter::tooManyAttempts($limiterKey, self::MAX_ATTEMPTS)) {
            return $this->error('RATE_LIMITED', 'Terlalu banyak percobaan, coba lagi nanti', 429)
                ->header('Retry-After', (string) RateLimiter::availableIn($limiterKey));
        }

        $hash = $this->config->get(AppConfigService::KEY_STUDENT_PASSCODE);

        if ($hash === null || ! Hash::check($request->validated('passcode'), $hash)) {
            RateLimiter::hit($limiterKey, self::DECAY_SECONDS);

            return $this->error('INVALID_PASSCODE', 'Passcode salah', 401);
        }

        RateLimiter::clear($limiterKey);

        $expiresAt = Carbon::now()->addSeconds(self::SESSION_TTL_SECONDS);
        $token = $this->sessions->issueToken($this->config->authEpoch(), $expiresAt);

        return $this->success([
            'role' => 'student',
            'expires_in' => self::SESSION_TTL_SECONDS,
        ])->withCookie(cookie(
            name: self::COOKIE_NAME,
            value: $token,
            minutes: self::SESSION_TTL_SECONDS / 60,
            path: '/',
            domain: null,
            secure: true,
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        ));
    }

    /**
     * POST /lock
     */
    public function lock(Request $request): RedirectResponse
    {
        $this->sessions->revokeRequestSession($request);

        return redirect()->route('landing')
            ->withCookie(cookie()->forget(self::COOKIE_NAME));
    }

    /**
     * DELETE /api/auth/passcode
     */
    public function logout(Request $request): JsonResponse
    {
        $this->sessions->revokeRequestSession($request);

        return $this->success(['message' => 'Logged out'])
            ->withCookie(cookie()->forget(self::COOKIE_NAME));
    }

    /**
     * GET /api/auth/session
     */
    public function session(Request $request): JsonResponse
    {
        return $this->success([
            'role' => $this->resolveRole($request),
        ]);
    }

    /**
     * A session is only valid if the token is authentic, unexpired, and
     * issued under the current auth epoch.
     */
    protected function resolveRole(Request $request): string
    {
        return $this->sessions->resolveRequestRole($request);
    }
}
