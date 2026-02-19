<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocalizedFortifyRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = app()->getLocale();
        $home = '/'.$locale;
        $dashboard = '/'.$locale.'/dashboard';

        config([
            'fortify.home' => $dashboard,
            'fortify.redirects.login' => $dashboard,
            'fortify.redirects.register' => $dashboard,
            'fortify.redirects.email-verification' => $dashboard,
            'fortify.redirects.password-confirmation' => $dashboard,
            'fortify.redirects.logout' => $home,
        ]);

        return $next($request);
    }
}
