<?php

use Inertia\Testing\AssertableInertia as Assert;

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

    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale')
        ->where('locale', 'en')
    );
});

test('root redirects to locale from cookie when user previously chose a language', function () {
    // Simulate user having chosen fa: request / with locale cookie set (call() passes cookies to request).
    // Should redirect to /fa, not /en.
    $response = $this->call('GET', '/', [], ['locale' => 'fa']);
    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('/fa');
});
