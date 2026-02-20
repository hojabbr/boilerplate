<?php

use App\Domains\Landing\Models\LandingSection;
use App\Domains\Pages\Models\Page;
use Inertia\Testing\AssertableInertia as Assert;

test('landing page returns 200 and Inertia props with locale and settings', function () {
    $response = $this->get('/en');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('welcome')
        ->has('locale')
        ->where('locale', 'en')
        ->has('settings')
        ->has('features')
        ->has('sections')
    );
});

test('landing page includes sections with translated content when sections exist', function () {
    LandingSection::create([
        'type' => 'hero',
        'sort_order' => 0,
        'title' => ['en' => 'Welcome to our site'],
        'subtitle' => ['en' => 'Build something great.'],
    ]);

    $response = $this->get('/en');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('welcome')
        ->has('sections')
        ->where('sections.0.type', 'hero')
        ->where('sections.0.title', 'Welcome to our site')
        ->where('sections.0.subtitle', 'Build something great.')
    );
});

test('landing page excludes inactive sections', function () {
    LandingSection::create([
        'type' => 'hero',
        'sort_order' => 0,
        'is_active' => true,
        'title' => ['en' => 'Active hero'],
    ]);
    LandingSection::create([
        'type' => 'cta',
        'sort_order' => 1,
        'is_active' => false,
        'title' => ['en' => 'Hidden CTA'],
    ]);

    $response = $this->get('/en');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('welcome')
        ->has('sections')
        ->where('sections', fn ($sections) => count($sections) === 1 && $sections[0]['type'] === 'hero' && $sections[0]['title'] === 'Active hero')
    );
});

test('nav_pages and footer_pages shared props reflect show_in_navigation and show_in_footer', function () {
    Page::create([
        'slug' => 'nav-only',
        'type' => 'custom',
        'title' => ['en' => 'Nav Page'],
        'is_active' => true,
        'show_in_navigation' => true,
        'show_in_footer' => false,
        'order' => 0,
    ]);
    Page::create([
        'slug' => 'footer-only',
        'type' => 'custom',
        'title' => ['en' => 'Footer Page'],
        'is_active' => true,
        'show_in_navigation' => false,
        'show_in_footer' => true,
        'order' => 1,
    ]);
    Page::create([
        'slug' => 'inactive-page',
        'type' => 'custom',
        'title' => ['en' => 'Inactive'],
        'is_active' => false,
        'show_in_navigation' => true,
        'show_in_footer' => true,
        'order' => 2,
    ]);

    $response = $this->get('/en');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('nav_pages')
        ->has('footer_pages')
        ->where('nav_pages', fn ($nav) => count($nav) === 1 && $nav[0]['slug'] === 'nav-only' && $nav[0]['title'] === 'Nav Page')
        ->where('footer_pages', fn ($footer) => count($footer) === 1 && $footer[0]['slug'] === 'footer-only' && $footer[0]['title'] === 'Footer Page')
    );
});
