<?php

namespace App\Core\Middleware;

use App\Core\Models\Setting;
use App\Domains\Page\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
     * Absolute URLs per locale for hreflang link tags.
     *
     * @return array<int, array{code: string, url: string}>
     */
    private function hreflangUrls(): array
    {
        $supported = config('laravellocalization.supportedLocales', []);

        $urls = [];
        foreach (array_keys($supported) as $code) {
            $code = (string) $code;
            $path = LaravelLocalization::getLocalizedURL($code, null, [], true);
            $absolute = $path !== false ? (str_starts_with($path, 'http') ? $path : url($path)) : url('/');

            $urls[] = [
                'code' => $code,
                'url' => $absolute,
            ];
        }

        return $urls;
    }

    /**
     * Nav and footer page links per locale (slug + translated title). Cached and invalidated by PageObserver.
     *
     * @return array{nav: array<string, array<int, array{slug: string, title: string}>>, footer: array<string, array<int, array{slug: string, title: string}>>}
     */
    private function menuPages(): array
    {
        return Cache::remember('menu_pages', 3600, function () {
            $supported = config('laravellocalization.supportedLocales', []);
            $locales = array_keys($supported);
            $navByLocale = [];
            $footerByLocale = [];
            foreach ($locales as $localeKey) {
                $locale = (string) $localeKey;
                $navByLocale[$locale] = Page::query()
                    ->active()
                    ->where('show_in_navigation', true)
                    ->orderBy('order')
                    ->get()
                    ->map(fn (Page $p) => [
                        'slug' => $p->slug,
                        'title' => (string) ($p->getTranslation('title', $locale) ?: $p->slug),
                    ])
                    ->values()
                    ->all();
                $footerByLocale[$locale] = Page::query()
                    ->active()
                    ->where('show_in_footer', true)
                    ->orderBy('order')
                    ->get()
                    ->map(fn (Page $p) => [
                        'slug' => $p->slug,
                        'title' => (string) ($p->getTranslation('title', $locale) ?: $p->slug),
                    ])
                    ->values()
                    ->all();
            }

            return ['nav' => $navByLocale, 'footer' => $footerByLocale];
        });
    }

    /**
     * Translation keys shared with every Inertia page (nav, auth, common, settings).
     *
     * @return array<int, string>
     */
    private function sharedTranslationKeys(): array
    {
        return [
            'nav.home',
            'nav.about',
            'nav.privacy',
            'nav.blog',
            'nav.contact',
            'nav.dashboard',
            'nav.login',
            'nav.register',
            'nav.open_menu',
            'nav.terms',
            'nav.settings',
            'common.dashboard',
            'common.back',
            'common.build_something_great',
            'common.app_fallback',
            'auth.login_title',
            'auth.login_description',
            'auth.login',
            'auth.register_title',
            'auth.register_description',
            'auth.register',
            'auth.forgot_title',
            'auth.forgot_description',
            'auth.reset_title',
            'auth.reset_description',
            'auth.confirm_title',
            'auth.confirm_description',
            'auth.verify_title',
            'auth.verify_description',
            'auth.two_factor_title',
            'auth.two_factor_recovery_title',
            'auth.two_factor_recovery_description',
            'auth.two_factor_code_title',
            'auth.two_factor_code_description',
            'auth.email',
            'auth.password',
            'auth.placeholder_email',
            'auth.placeholder_password',
            'auth.placeholder_confirm_password',
            'auth.placeholder_full_name',
            'auth.remember_me',
            'auth.sign_up',
            'auth.forgot_password',
            'auth.no_account',
            'auth.return_to_login',
            'auth.placeholder_recovery_code',
            'auth.email_password_reset_link',
            'auth.resend_verification_email',
            'auth.log_out',
            'auth.verification_link_sent_registration',
            'auth.confirm_password_button',
            'auth.continue',
            'auth.toggle_recovery_code',
            'auth.toggle_authentication_code',
            'auth.reset_password_button',
            'auth.create_account',
            'auth.already_have_account',
            'settings.title',
            'settings.description',
            'settings.nav_profile',
            'settings.nav_password',
            'settings.nav_two_factor',
            'settings.nav_appearance',
            'settings.profile_title',
            'settings.profile_heading',
            'settings.profile_info_title',
            'settings.profile_info_description',
            'settings.password_title',
            'settings.password_heading',
            'settings.password_update_title',
            'settings.password_update_description',
            'settings.appearance_title',
            'settings.appearance_heading',
            'settings.appearance_description',
            'settings.two_factor_title',
            'settings.two_factor_heading',
            'settings.two_factor_description',
            'settings.two_factor_modal_enabled_title',
            'settings.two_factor_modal_enabled_description',
            'settings.two_factor_modal_verify_title',
            'settings.two_factor_modal_verify_description',
            'settings.two_factor_modal_enable_title',
            'settings.two_factor_modal_enable_description',
            'settings.two_factor_manual_code',
            'settings.close',
            'settings.confirm',
            'settings.recovery_codes_title',
            'settings.recovery_codes_description',
            'settings.view_recovery_codes',
            'settings.hide_recovery_codes',
            'settings.regenerate_codes',
            'settings.recovery_codes_warning',
            'sidebar.dashboard',
            'sidebar.repository',
            'sidebar.documentation',
            'settings.enabled',
            'settings.disabled',
            'settings.label_name',
            'settings.label_email',
            'settings.placeholder_name',
            'settings.placeholder_email',
            'settings.placeholder_current_password',
            'settings.placeholder_new_password',
            'settings.placeholder_confirm_password',
            'settings.email_unverified',
            'settings.send_verification',
            'settings.verification_link_sent',
            'settings.save',
            'settings.saved',
            'settings.save_password',
            'settings.enable_2fa',
            'settings.disable_2fa',
            'settings.continue_setup',
            'settings.two_factor_enabled_description',
            'settings.two_factor_disabled_description',
            'settings.delete_account_title',
            'settings.delete_account_description',
            'settings.delete_account_warning',
            'settings.delete_account_warning_description',
            'settings.delete_account_confirm_password',
            'settings.delete_account_cancel',
            'settings.delete_account_confirm_title',
            'settings.delete_account_confirm_description',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function sharedTranslations(): array
    {
        $out = [];
        foreach ($this->sharedTranslationKeys() as $key) {
            $out[$key] = __($key);
        }

        return $out;
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
        $setting = Setting::site();
        $siteName = $setting->company_name ?: config('app.name');
        $siteTagline = $setting->tagline ?: config('app.description');
        View::share('siteName', $siteName);
        View::share('siteTagline', $siteTagline);

        return [
            ...parent::share($request),
            'name' => $siteName,
            'site_tagline' => $siteTagline,
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => $locale,
            'dir' => in_array($locale, $rtlLocales, true) ? 'rtl' : 'ltr',
            'supportedLocales' => config('laravellocalization.supportedLocales', []),
            'locale_switch_urls' => $this->localeSwitchUrls(),
            'canonical_url' => $request->url(),
            'hreflang_urls' => $this->hreflangUrls(),
            'default_locale' => config('app.locale'),
            'translations' => $this->sharedTranslations(),
            'nav_pages' => ($menu = $this->menuPages())['nav'][$locale] ?? [],
            'footer_pages' => $menu['footer'][$locale] ?? [],
        ];
    }
}
