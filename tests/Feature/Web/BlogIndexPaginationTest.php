<?php

use App\Core\Models\Language;
use App\Domains\Blog\Models\BlogPost;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Pennant\Feature;

beforeEach(function () {
    Feature::activate('blog');
});

test('blog index returns paginated posts with data links and meta', function () {
    refreshApplicationWithLocale('en');

    config(['blog.posts_per_page' => 2]);

    $en = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'script' => 'Latn', 'is_default' => true, 'sort_order' => 0]
    );

    BlogPost::factory()->count(3)->create([
        'language_id' => $en->id,
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('blog/Index')
        ->has('posts')
        ->has('posts.data')
        ->has('posts.links')
        ->where('posts.current_page', 1)
        ->where('posts.last_page', 2)
        ->where('posts.per_page', 2)
    );

    $posts = $response->inertiaProps('posts');
    expect($posts['data'])->toHaveCount(2);
});

test('blog index page 2 returns next page of posts', function () {
    refreshApplicationWithLocale('en');

    config(['blog.posts_per_page' => 2]);

    $en = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'script' => 'Latn', 'is_default' => true, 'sort_order' => 0]
    );

    BlogPost::factory()->count(3)->create([
        'language_id' => $en->id,
        'published_at' => now(),
    ]);

    $response = $this->get(route('blog.index', ['page' => 2]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('blog/Index')
        ->has('posts')
        ->where('posts.current_page', 2)
        ->where('posts.last_page', 2)
    );

    $data = $response->inertiaProps('posts')['data'];
    expect($data)->toHaveCount(1);
    expect($data[0])->toHaveKeys(['slug', 'title', 'excerpt', 'published_at']);
});
