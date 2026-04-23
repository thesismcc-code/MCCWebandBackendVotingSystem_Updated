<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.jwt'     => \App\Http\Middleware\JwtMiddleware::class,
            'auth.session' => \App\Http\Middleware\SessionAuthMiddleware::class,
        ]);
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);
        $middleware->validateCsrfTokens(except: [
            'validate-login',
            'login',
            'student-validate-login',
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch gracefully
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please refresh the page and try again.',
                ], 419);
            }
            
            return redirect()->back()
                ->withInput($request->except('_token', 'password'))
                ->with('error', 'Your session has expired. Please try again.');
        });
    })->create();

