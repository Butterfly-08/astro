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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.guest' => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'approved.astrologer' => \App\Http\Middleware\ApprovedAstrologerMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('astrologer') || $request->is('astrologer/*')) {
                return route('astrologer.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.dashboard');
            }
            if (auth('web')->check() && auth('web')->user()->isAstrologer()) {
                return route('astrologer.dashboard');
            }
            return route('user.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
