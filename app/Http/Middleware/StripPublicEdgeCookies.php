<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Strips Set-Cookie headers on publicly edge-cacheable responses (Cache-Control: public + s-maxage).
 *
 * Vercel Edge CDN forces `x-vercel-cache: BYPASS` whenever a response includes Set-Cookie,
 * and caching Set-Cookie publicly would also risk leaking session/CSRF cookies across visitors.
 */
class StripPublicEdgeCookies
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->headers->hasCacheControlDirective('public')
            && $response->headers->hasCacheControlDirective('s-maxage')) {
            $response->headers->remove('Set-Cookie');
        }

        return $response;
    }
}
