<?php

use App\Core\Models\Language;
use App\Domains\Testimonial\Models\Testimonial;
use Laravel\Pennant\Feature;

beforeEach(function () {
    $this->locale = 'en';
});

test('testimonials show returns 404 when testimonials feature is inactive', function () {
    Feature::deactivate('testimonials');

    $response = $this->get("/{$this->locale}/testimonials");

    $response->assertNotFound();
});

test('testimonials show returns 200 and inertia testimonials show when feature is active', function () {
    Feature::activate('testimonials');

    $language = Language::firstOrCreate(
        ['code' => $this->locale],
        ['name' => 'English', 'sort_order' => 0]
    );
    Testimonial::create([
        'language_id' => $language->id,
        'quote' => 'A great product.',
        'author' => 'Jane Doe',
        'role' => 'CTO',
        'sort_order' => 0,
    ]);

    $response = $this->get("/{$this->locale}/testimonials");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('testimonials/Show')
        ->has('items')
        ->has('seo')
        ->where('items.0.quote', 'A great product.')
        ->where('items.0.author', 'Jane Doe')
        ->where('items.0.role', 'CTO')
    );
});
