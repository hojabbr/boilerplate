<?php

use App\Core\Models\Language;
use App\Domains\Faq\Models\Faq;
use Laravel\Pennant\Feature;

beforeEach(function () {
    $this->locale = 'en';
});

test('faq show returns 404 when faq feature is inactive', function () {
    Feature::deactivate('faq');

    $response = $this->get("/{$this->locale}/faq");

    $response->assertNotFound();
});

test('faq show returns 200 and inertia faq show when feature is active', function () {
    Feature::activate('faq');

    $language = Language::firstOrCreate(
        ['code' => $this->locale],
        ['name' => 'English', 'sort_order' => 0]
    );
    Faq::create([
        'language_id' => $language->id,
        'question' => 'Test question?',
        'answer' => 'Test answer.',
        'sort_order' => 0,
    ]);

    $response = $this->get("/{$this->locale}/faq");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('faq/Show')
        ->has('items')
        ->has('seo')
        ->where('items.0.question', 'Test question?')
        ->where('items.0.answer', 'Test answer.')
    );
});
