<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckDatabaseToken; // ✅ ADDed this line to compare the request token with token saved in database

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
    $middleware->validateCsrfTokens(except: [
        'api/*',
    ]);

            // ✅ ADD THIS SECTION - Register your middleware alias
        $middleware->alias([
            'check.token' => CheckDatabaseToken::class,
        ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
