<?php

namespace App\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthFeaturesEnabled
{
    /**
     * Gate login and registration routes by Pennant feature flags.
     * When login is disabled, allow login if the intended destination is the Filament admin panel
     * so admins can still sign in.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        if ($routeName === null) {
            return $next($request);
        }

        if (in_array($routeName, ['login', 'login.store'], true) && ! Feature::for(null)->active('login')) {
            if ($this->intendedPathIsAdminPanel($request)) {
                return $next($request);
            }
            abort(404);
        }

        if (in_array($routeName, ['register', 'register.store'], true) && ! Feature::for(null)->active('registration')) {
            abort(404);
        }

        return $next($request);
    }

    /**
     * Whether the session's intended URL points to the Filament admin panel.
     */
    private function intendedPathIsAdminPanel(Request $request): bool
    {
        $intended = $request->session()->get('url.intended');
        if (! is_string($intended) || $intended === '') {
            return false;
        }
        $path = parse_url($intended, PHP_URL_PATH);
        if (! is_string($path)) {
            return false;
        }
        $adminPath = '/admin';

        return $path === $adminPath || str_starts_with($path, $adminPath.'/');
    }
}
