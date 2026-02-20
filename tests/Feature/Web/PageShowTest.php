<?php

use App\Models\Page;
use Inertia\Testing\AssertableInertia as Assert;

test('page show returns correct content for locale and slug', function () {
    refreshApplicationWithLocale('en');

    Page::create([
        'slug' => 'test-page',
        'title' => ['en' => 'Test Page Title'],
        'body' => ['en' => '<p>Test body</p>'],
        'type' => 'custom',
    ]);

    $response = $this->get('/en/page/test-page');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('pages/Show')
        ->has('page')
        ->where('page.title', 'Test Page Title')
        ->where('page.body', '<p>Test body</p>')
    );
});

test('page show returns 404 for missing slug', function () {
    $response = $this->get('/en/page/non-existent-slug');

    $response->assertNotFound();
});
