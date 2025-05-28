<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string>
     */
    protected $middleware = [
        // \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        // \Illuminate\Http\Middleware\TrimStrings::class,
        // \Illuminate\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \App\Http\Middleware\VerifyCsrfToken::class, // CSRF token now registered in bootstrap/app.php in Laravel 11/12
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or individual routes.
     *
     * @var array<string, class-string>
     */
    protected $middlewareAliases = [
        // ... ใน protected $middlewareAliases = [ ... ];
'auth' => \App\Http\Middleware\Authenticate::class,
'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
'password.confirm' => \Illuminate\Auth\Middleware\RequirePasswordConfirmation::class,
'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

// --- เพิ่ม Middleware ใหม่ของคุณตรงนี้ ---
'has_role' => \App\Http\Middleware\HasRole::class, // <--- เพิ่มบรรทัดนี้
// ----------------------------------------------------
    ];
}