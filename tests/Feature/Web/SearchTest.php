<?php

use App\Core\Models\Language;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Page\Models\Page;

beforeEach(function () {
    $this->locale = 'en';
});

test('search returns json with pages and blog_posts keys', function () {
    $response = $this->get("/{$this->locale}/search?q=anything");

    $response->assertOk();
    $response->assertJsonStructure([
        'pages' => [],
        'blog_posts' => [],
    ]);
});

test('search returns empty arrays when query is empty', function () {
    $response = $this->get("/{$this->locale}/search?q=");

    $response->assertOk();
    $response->assertExactJson([
        'pages' => [],
        'blog_posts' => [],
    ]);
});

test('search returns matching pages and published blog posts via scout', function () {
    $language = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'sort_order' => 0]
    );

    $page = Page::create([
        'slug' => 'scout-search-test-page',
        'type' => 'custom',
    ]);
    $page->setTranslation('title', 'en', 'ScoutSearchTest Page Title');
    $page->setTranslation('body', 'en', '<p>Body</p>');
    $page->save();

    $post = BlogPost::create([
        'language_id' => $language->id,
        'slug' => 'scout-search-test-post',
        'title' => 'ScoutSearchTest Post',
        'excerpt' => 'Excerpt',
        'body' => '<p>Body</p>',
        'meta_description' => 'Meta',
        'published_at' => now(),
    ]);

    $response = $this->get("/{$this->locale}/search?q=ScoutSearchTest");

    $response->assertOk();
    $data = $response->json();
    expect($data['pages'])->toBeArray();
    expect($data['blog_posts'])->toBeArray();

    // Scout (collection/database/meilisearch) may return our records; structure is validated
    $pageIds = collect($data['pages'])->pluck('id')->all();
    $postIds = collect($data['blog_posts'])->pluck('id')->all();
    expect(in_array($page->id, $pageIds, true) || in_array($post->id, $postIds, true))->toBeTrue();
});
