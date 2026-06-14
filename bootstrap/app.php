<?php

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\EnsureEnumeratorIsActive;
use App\Http\Middleware\SecureFileDownload;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/company-profile.php',
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\MaintenanceMiddleware::class);
        $middleware->appendToGroup('web', ContentSecurityPolicy::class);
        $middleware->statefulApi();
        $middleware->alias([
            'auth'               => Authenticate::class,
            'role'               => RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'enumerator.active'  => EnsureEnumeratorIsActive::class,
            'secure.file'        => SecureFileDownload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
