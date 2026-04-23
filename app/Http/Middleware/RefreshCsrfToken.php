<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request and refresh CSRF token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Regenerate CSRF token on every request to prevent expiration
        if ($request->isMethod('GET') && !$request->ajax()) {
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
