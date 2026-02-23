<?php

use App\Core\Models\Language;
use App\Core\Services\SupportedLocalesService;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;
use Mcamara\LaravelLocalization\LaravelLocalization;

test('localized home route returns locale in shared props', function () {
    refreshApplicationWithLocale('en');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale')
        ->where('locale', 'en')
        ->has('supportedLocales')
        ->has('locale_switch_urls')
    );
});

test('localized route returns locale_switch_urls with correct structure and locale prefixes', function () {
    refreshApplicationWithLocale('en');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale_switch_urls')
        ->where('locale', 'en')
    );

    $urls = $response->inertiaProps('locale_switch_urls');
    expect($urls)->toBeArray();
    foreach ($urls as $item) {
        expect($item)->toHaveKeys(['code', 'name', 'native', 'url']);
        expect($item['url'])->toBeString();
    }
    $enUrl = collect($urls)->firstWhere('code', 'en');
    expect($enUrl)->not->toBeNull();
    expect($enUrl['url'])->toContain('/en');
});

test('localized dashboard route returns locale in shared props', function () {
    refreshApplicationWithLocale('en');

    $user = \App\Domains\Auth\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale')
        ->where('locale', 'en')
    );
});

test('root redirects to locale from cookie when user previously chose a language', function () {
    // Supported locales come from DB; ensure en and fa exist and are enabled so LocaleCookieRedirect and Inertia see them.
    Language::factory()->create(['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'sort_order' => 0, 'is_default' => true, 'is_enabled' => true]);
    Language::factory()->create(['code' => 'fa', 'name' => 'Persian', 'direction' => 'rtl', 'sort_order' => 1, 'is_enabled' => true]);
    app(SupportedLocalesService::class)->clearCache();
    Config::set('laravellocalization.supportedLocales', app(SupportedLocalesService::class)->get());
    app()->forgetInstance(LaravelLocalization::class);
    app()->forgetInstance('laravellocalization');

    // Simulate user having chosen fa: request / with locale cookie set. Should redirect to /fa, not /en.
    $response = $this->call('GET', '/', [], ['locale' => 'fa']);
    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('/fa');
});

test('root with cookie for disabled locale redirects to default not to disabled locale', function () {
    // en enabled and default; fa disabled. Cookie says fa but fa is not in supported locales.
    Language::factory()->create(['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'sort_order' => 0, 'is_default' => true, 'is_enabled' => true]);
    Language::factory()->create(['code' => 'fa', 'name' => 'Persian', 'direction' => 'rtl', 'sort_order' => 1, 'is_enabled' => false]);
    app(SupportedLocalesService::class)->clearCache();
    Config::set('laravellocalization.supportedLocales', app(SupportedLocalesService::class)->get());
    app()->forgetInstance(LaravelLocalization::class);
    app()->forgetInstance('laravellocalization');

    $response = $this->call('GET', '/', [], ['locale' => 'fa']);
    $response->assertRedirect();
    // Should redirect to default (en), not to /fa.
    expect($response->headers->get('Location'))->not->toContain('/fa');
    expect($response->headers->get('Location'))->toContain('/en');
});
