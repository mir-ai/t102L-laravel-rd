<?php

use Illuminate\Http\Request;
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
        // MIRAIE obata@mir-ai.co.jp
        // https://laravel.com/docs/12.x/requests#configuring-trusted-proxies
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
