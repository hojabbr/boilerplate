<?php

use App\Core\Models\Language;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Queries\GetPublishedPosts;
use Laravel\Pennant\Feature;

beforeEach(function () {
    Feature::activate('blog');
});

test('get published posts returns paginated posts for current locale', function () {
    refreshApplicationWithLocale('en');

    $en = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'script' => 'Latn', 'is_default' => true, 'sort_order' => 0]
    );

    BlogPost::factory()->count(3)->create([
        'language_id' => $en->id,
        'published_at' => now(),
    ]);

    config(['blog.posts_per_page' => 2]);

    $query = app(GetPublishedPosts::class);
    $result = $query->handle();

    expect($result->count())->toBe(2);
    expect($result->total())->toBe(3);
    expect($result->currentPage())->toBe(1);
    expect($result->lastPage())->toBe(2);
});

test('blog index returns 404 when blog feature is inactive', function () {
    Feature::deactivate('blog');

    $this->get(route('blog.index'))->assertNotFound();
});
