<?php

namespace Ermtool\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Ermtool\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //comment out to avoid CSRF Token mismatch error
        //\Ermtool\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Ermtool\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \Ermtool\Http\Middleware\RedirectIfAuthenticated::class,
        'cors' => \Ermtool\Http\Middleware\Cors::class,
        'api' => \Ermtool\Http\Middleware\ApiMiddleware::class,
        'csrf' => \Ermtool\Http\Middleware\VerifyCsrfToken::class // add it as a middleware route 
    ];
}
