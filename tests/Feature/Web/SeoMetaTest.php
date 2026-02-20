<?php

use Inertia\Testing\AssertableInertia as Assert;

test('shared props include canonical_url and hreflang_urls for SEO', function () {
    refreshApplicationWithLocale('en');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('canonical_url')
        ->has('hreflang_urls')
        ->has('default_locale')
    );

    $canonicalUrl = $response->inertiaProps('canonical_url');
    expect($canonicalUrl)->toBeString();
    expect($canonicalUrl)->toContain('/en');

    $hreflangUrls = $response->inertiaProps('hreflang_urls');
    expect($hreflangUrls)->toBeArray();
    foreach ($hreflangUrls as $item) {
        expect($item)->toHaveKeys(['code', 'url']);
        expect($item['url'])->toBeString();
    }
});

test('welcome page has seo prop with title and description from settings', function () {
    refreshApplicationWithLocale('en');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('welcome')
        ->has('seo')
        ->has('seo.description')
    );
    $title = $response->inertiaProps('seo')['title'] ?? '';
    expect($title)->toContain('Welcome');
});

test('contact page has seo prop with title and description', function () {
    refreshApplicationWithLocale('en');

    \Laravel\Pennant\Feature::activate('contact-form');

    $response = $this->get(route('contact.show'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('contact/Show')
        ->has('seo')
        ->has('seo.description')
    );
    $title = $response->inertiaProps('seo')['title'] ?? '';
    expect($title)->toContain('Contact');
});

test('blog index has seo prop with title and description', function () {
    refreshApplicationWithLocale('en');

    \Laravel\Pennant\Feature::activate('blog');

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('blog/Index')
        ->has('seo')
        ->has('seo.description')
    );
    $title = $response->inertiaProps('seo')['title'] ?? '';
    expect($title)->toContain('Blog');
});
