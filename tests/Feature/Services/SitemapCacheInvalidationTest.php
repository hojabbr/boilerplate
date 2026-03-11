<?php

use App\Core\Models\Language;
use App\Core\Services\SitemapGenerator;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Faq\Models\Faq;
use App\Domains\Page\Models\Page;
use App\Domains\Testimonial\Models\Testimonial;
use Laravel\Pennant\Feature;

beforeEach(function () {
    $this->language = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'script' => 'Latn', 'is_default' => true, 'is_enabled' => true, 'sort_order' => 0]
    );

    Feature::activate('blog');
    Feature::activate('page');
    Feature::activate('faq');
    Feature::activate('testimonials');

    SitemapGenerator::clearCache();
});

it('clears cache when blog post is created', function () {
    $this->get('/sitemap.xml');

    $post = BlogPost::factory()->for($this->language)->published()->create();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain($post->slug);
});

it('clears cache when blog post is updated', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();

    $this->get('/sitemap.xml');

    $post->update(['slug' => 'updated-slug']);

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain('updated-slug');
});

it('clears cache when blog post is deleted', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();

    $this->get('/sitemap.xml');

    $post->delete();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->not->toContain($post->slug);
});

it('clears cache when blog post is restored', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();

    $post->delete();
    $this->get('/sitemap.xml');

    $post->restore();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain($post->slug);
});

it('clears cache when page is created', function () {
    $this->get('/sitemap.xml');

    $page = Page::factory()->active()->create();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain($page->slug);
});

it('clears cache when page is updated', function () {
    $page = Page::factory()->active()->create();

    $this->get('/sitemap.xml');

    $page->update(['slug' => 'updated-page-slug']);

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain('updated-page-slug');
});

it('clears cache when page is deleted', function () {
    $page = Page::factory()->active()->create();

    $this->get('/sitemap.xml');

    $page->delete();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->not->toContain($page->slug);
});

it('clears cache when faq is created', function () {
    $this->get('/sitemap.xml');

    Faq::factory()->for($this->language)->create();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain('/faq');
});

it('clears cache when faq is deleted', function () {
    Faq::factory()->for($this->language)->create();

    $this->get('/sitemap.xml');

    Faq::where('language_id', $this->language->id)->delete();

    $this->get('/sitemap.xml')->assertStatus(200);
});

it('clears cache when testimonial is created', function () {
    $this->get('/sitemap.xml');

    Testimonial::factory()->for($this->language)->create();

    $response = $this->get('/sitemap.xml');
    expect($response->getContent())->toContain('/testimonials');
});

it('clears cache when testimonial is deleted', function () {
    Testimonial::factory()->for($this->language)->create();

    $this->get('/sitemap.xml');

    Testimonial::where('language_id', $this->language->id)->delete();

    $this->get('/sitemap.xml')->assertStatus(200);
});

it('regenerates sitemap with new etag after cache invalidation', function () {
    $response1 = $this->get('/sitemap.xml');
    $etag1 = $response1->headers->get('ETag');

    BlogPost::factory()->for($this->language)->published()->create();

    $response2 = $this->get('/sitemap.xml');
    $etag2 = $response2->headers->get('ETag');

    expect($etag1)->not->toBe($etag2);
});

it('returns same etag for cached content', function () {
    BlogPost::factory()->for($this->language)->published()->create();

    $response1 = $this->get('/sitemap.xml');
    $etag1 = $response1->headers->get('ETag');

    $response2 = $this->get('/sitemap.xml');
    $etag2 = $response2->headers->get('ETag');

    expect($etag1)->toBe($etag2);
});
