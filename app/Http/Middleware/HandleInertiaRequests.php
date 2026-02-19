<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<int, array{code: string, name: string, native: string, url: string}>
     */
    private function localeSwitchUrls(): array
    {
        $supported = config('laravellocalization.supportedLocales', []);

        $urls = [];
        foreach ($supported as $code => $props) {
            $url = LaravelLocalization::getLocalizedURL($code, null, [], true);

            $urls[] = [
                'code' => $code,
                'name' => (string) ($props['name'] ?? $code),
                'native' => (string) ($props['native'] ?? $code),
                'url' => $url !== false ? $url : '',
            ];
        }

        return $urls;
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $rtlLocales = ['ar', 'fa'];

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => $locale,
            'dir' => in_array($locale, $rtlLocales, true) ? 'rtl' : 'ltr',
            'supportedLocales' => config('laravellocalization.supportedLocales', []),
            'locale_switch_urls' => $this->localeSwitchUrls(),
        ];
    }
}
