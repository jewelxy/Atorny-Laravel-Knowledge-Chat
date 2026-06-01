<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    // Routing configuration for an API-first architecture
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // Single middleware configuration block
    ->withMiddleware(function (Middleware $middleware) {
        // Append only — `use()` replaces the default stack and removes HandleCors.
        $middleware->append([
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Preserve web redirect behavior while keeping API responses JSON-first
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('api/*') ? null : '/login';
        });

        // Spatie RBAC aliases for role and permission protection
        $middleware->alias([
            'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'response.cache' => \App\Http\Middleware\ResponseCacheMiddleware::class,
        ]);
    })

    // API-first exception rendering for JSON responses
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnauthorizedException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden. You do not have the required role or permission.',
                ], 403);
            }

            return null;
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return null;
        });
    })

    ->create();
