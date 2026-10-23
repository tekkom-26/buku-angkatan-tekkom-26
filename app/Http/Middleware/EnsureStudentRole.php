<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->attributes->get('student_role') !== 'student') {
            return redirect()->route('unlock');
        }

        return $next($request);
    }
}
