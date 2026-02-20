<?php

namespace App\Core\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies password confirmation only when Fortify two-factor "confirmPassword" option is enabled.
 * Allows the two-factor settings route to be evaluated per-request.
 */
class EnsureTwoFactorPasswordConfirm
{
    public function __construct(
        protected RequirePassword $requirePassword
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            return $next($request);
        }

        return $this->requirePassword->handle($request, $next);
    }
}
