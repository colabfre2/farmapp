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
        
        // Alias middleware lu (jangan dihapus)
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        
        $middleware->statefulApi();

        // ── BEBASKAN ROUTE MIDTRANS WEBHOOK DARI CSRF DI SINI 🔥 ──
        $middleware->validateCsrfTokens(except: [
            'payment/notification', 
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();