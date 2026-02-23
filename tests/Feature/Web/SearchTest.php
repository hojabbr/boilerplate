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

    $page = Page::withoutSyncingToSearch(function () {
        $p = Page::create([
            'slug' => 'scout-search-test-page',
            'type' => 'custom',
            'is_active' => true,
        ]);
        $p->setTranslation('title', 'en', 'ScoutSearchTest Page Title');
        $p->setTranslation('body', 'en', '<p>Body</p>');
        $p->save();

        return $p;
    });
    $page->searchableSync();

    $post = BlogPost::withoutSyncingToSearch(function () use ($language) {
        return BlogPost::create([
            'language_id' => $language->id,
            'slug' => 'scout-search-test-post',
            'title' => 'ScoutSearchTest Post',
            'excerpt' => 'Excerpt',
            'body' => '<p>Body</p>',
            'meta_description' => 'Meta',
            'published_at' => now(),
        ]);
    });
    $post->searchableSync();

    $response = $this->get("/{$this->locale}/search?q=ScoutSearchTest");

    $response->assertOk();
    $data = $response->json();
    expect($data['pages'])->toBeArray();
    expect($data['blog_posts'])->toBeArray();

    $pageIds = collect($data['pages'])->pluck('id')->all();
    $postIds = collect($data['blog_posts'])->pluck('id')->all();
    expect($pageIds)->toContain($page->id);
    expect($postIds)->toContain($post->id);
});

test('search does not return inactive pages', function () {
    $inactive = Page::withoutSyncingToSearch(function () {
        $p = Page::create([
            'slug' => 'scout-inactive-page',
            'type' => 'custom',
            'is_active' => false,
        ]);
        $p->setTranslation('title', 'en', 'ScoutInactivePage Title');
        $p->setTranslation('body', 'en', 'Body');
        $p->save();

        return $p;
    });
    $inactive->searchableSync();

    $response = $this->get("/{$this->locale}/search?q=ScoutInactivePage");

    $response->assertOk();
    $data = $response->json();
    $pageIds = collect($data['pages'])->pluck('id')->all();
    expect($pageIds)->not->toContain($inactive->id);
});

test('search does not return soft-deleted pages or posts', function () {
    $language = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'sort_order' => 0]
    );

    $deletedPage = Page::withoutSyncingToSearch(function () {
        $p = Page::create([
            'slug' => 'scout-deleted-page',
            'type' => 'custom',
            'is_active' => true,
        ]);
        $p->setTranslation('title', 'en', 'ScoutDeletedPage Title');
        $p->setTranslation('body', 'en', 'Body');
        $p->save();

        return $p;
    });
    $deletedPage->searchableSync();
    $deletedPage->delete();

    $deletedPost = BlogPost::withoutSyncingToSearch(function () use ($language) {
        return BlogPost::create([
            'language_id' => $language->id,
            'slug' => 'scout-deleted-post',
            'title' => 'ScoutDeletedPost',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'meta_description' => 'Meta',
            'published_at' => now(),
        ]);
    });
    $deletedPost->searchableSync();
    $deletedPost->delete();

    $response = $this->get("/{$this->locale}/search?q=ScoutDeleted");

    $response->assertOk();
    $data = $response->json();
    $pageIds = collect($data['pages'])->pluck('id')->all();
    $postIds = collect($data['blog_posts'])->pluck('id')->all();
    expect($pageIds)->not->toContain($deletedPage->id);
    expect($postIds)->not->toContain($deletedPost->id);
});
