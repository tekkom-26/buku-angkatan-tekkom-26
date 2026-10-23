<?php

namespace App\Http\Middleware;

use App\Services\StudentSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthEpoch
{
    public function __construct(protected StudentSessionService $sessions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('student_role', $this->sessions->resolveRequestRole($request));

        return $next($request);
    }
}
