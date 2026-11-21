<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\AppConfigService;
use App\Services\StudentSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Early State-Version ETag & Edge Cache-Control middleware.
 *
 * Computes a Weak ETag from fullUrl + app_state_version + studentRole BEFORE
 * executing the Controller and rendering Blade, returning 304 Not Modified
 * immediately when the client's If-None-Match header matches.
 */
class EarlyStateEtag
{
    public const CACHE_CONTROL_PUBLIC = 'public, max-age=0, s-maxage=60, stale-while-revalidate=600';

    public const CACHE_CONTROL_PRIVATE = 'private, no-cache, must-revalidate';

    public function __construct(
        protected AppConfigService $config,
        protected StudentSessionService $sessions,
    ) {}

    public function handle(Request $request, Closure $next, string $visibility = 'public'): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $studentRole = (string) ($request->attributes->get('student_role')
            ?? $this->sessions->resolveRequestRole($request));

        $request->attributes->set('student_role', $studentRole);

        $stateVersion = $this->config->stateVersion();
        $etag = 'W/"'.sha1($request->fullUrl().'|'.$stateVersion.'|'.$studentRole).'"';
        $cacheControl = ($visibility === 'private' || $studentRole !== 'guest')
            ? self::CACHE_CONTROL_PRIVATE
            : self::CACHE_CONTROL_PUBLIC;

        if ($this->matchesIfNoneMatch($request, $etag)) {
            $notModified = response('', Response::HTTP_NOT_MODIFIED);
            $notModified->headers->set('ETag', $etag);
            $notModified->headers->set('Cache-Control', $cacheControl);

            return $notModified;
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            $response->headers->set('ETag', $etag);
            $response->headers->set('Cache-Control', $cacheControl);
        }

        return $response;
    }

    protected function matchesIfNoneMatch(Request $request, string $etag): bool
    {
        $ifNoneMatch = $request->headers->get('If-None-Match');

        if (! is_string($ifNoneMatch) || trim($ifNoneMatch) === '') {
            return false;
        }

        $candidates = array_map(trim(...), explode(',', $ifNoneMatch));

        foreach ($candidates as $candidate) {
            if ($candidate === '*' || $candidate === $etag) {
                return true;
            }
        }

        return false;
    }
}
