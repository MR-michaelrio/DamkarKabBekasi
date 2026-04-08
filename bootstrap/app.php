<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('ambulance/*') || $request->is('driver/*')) {
                return route('ambulance.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function () {
            if (auth()->guard('ambulance')->check()) {
                return route('driver.dashboard');
            }
            return route('dashboard');
        });

        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'logout',
            'ambulance/logout',
            'ambulance/login',
            'public-fcm-token',
            'driver/fcm-token',
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('portal');
        });
    })
    ->create();
