<?php

use App\Core\Models\Language;
use App\Core\Services\SitemapGenerator;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Page\Models\Page;
use Laravel\Pennant\Feature;

beforeEach(function () {
    $this->language = Language::firstOrCreate(
        ['code' => 'en'],
        ['name' => 'English', 'script' => 'Latn', 'is_default' => true, 'is_enabled' => true, 'sort_order' => 0]
    );

    Feature::activate('blog');
    Feature::activate('page');
    Feature::activate('contact-form');
    Feature::activate('faq');
    Feature::activate('testimonials');

    SitemapGenerator::clearCache();
});

it('returns sitemap xml response with correct content type', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
});

it('returns valid xml that can be parsed', function () {
    $response = $this->get('/sitemap.xml');

    $xml = $response->getContent();
    expect($xml)->toBeString();

    $parsed = simplexml_load_string($xml);
    expect($parsed)->toBeInstanceOf(SimpleXMLElement::class);
    expect($parsed->getName())->toBe('urlset');
});

it('includes published blog posts in sitemap', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();

    $response = $this->get('/sitemap.xml');

    expect($response->getContent())->toContain($post->slug);
});

it('includes active pages in sitemap', function () {
    $page = Page::factory()->active()->create();

    $response = $this->get('/sitemap.xml');

    expect($response->getContent())->toContain($page->slug);
});

it('sets correct cache headers', function () {
    $response = $this->get('/sitemap.xml');

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('public');
    expect($cacheControl)->toContain('max-age=86400');
    expect($response->headers->get('ETag'))->not->toBeNull();
});

it('is accessible without authentication', function () {
    auth()->logout();

    $this->get('/sitemap.xml')->assertStatus(200);
});

it('returns valid xml when all features are disabled', function () {
    Feature::deactivate('blog');
    Feature::deactivate('page');
    Feature::deactivate('contact-form');
    Feature::deactivate('faq');
    Feature::deactivate('testimonials');

    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    expect($parsed)->toBeInstanceOf(SimpleXMLElement::class);
    expect($parsed->getName())->toBe('urlset');
});

it('includes contact urls when contact-form feature is enabled', function () {
    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    $contactUrls = [];
    foreach ($parsed->url as $url) {
        if (str_contains((string) $url->loc, '/contact')) {
            $contactUrls[] = (string) $url->loc;
        }
    }

    expect($contactUrls)->not->toBeEmpty();
});

it('excludes contact urls when contact-form feature is disabled', function () {
    Feature::deactivate('contact-form');

    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    foreach ($parsed->url as $url) {
        expect((string) $url->loc)->not->toContain('/contact');
    }
});

it('includes priority and changefreq for all entries', function () {
    BlogPost::factory()->for($this->language)->published()->create();

    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    foreach ($parsed->url as $url) {
        expect((string) $url->changefreq)->not->toBeEmpty();
        expect((float) $url->priority)->toBeGreaterThan(0);
    }
});

it('generates absolute urls', function () {
    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    foreach ($parsed->url as $url) {
        expect((string) $url->loc)->toMatch('/^https?:\/\//');
    }
});

it('includes all enabled locales in sitemap', function () {
    Language::factory()->state(['code' => 'fr', 'is_enabled' => true])->create();

    SitemapGenerator::clearCache();

    $response = $this->get('/sitemap.xml');
    $parsed = simplexml_load_string($response->getContent());

    $urls = [];
    foreach ($parsed->url as $url) {
        $urls[] = (string) $url->loc;
    }

    $hasEnglish = array_filter($urls, fn (string $url) => str_contains($url, '/en'));
    $hasFrench = array_filter($urls, fn (string $url) => str_contains($url, '/fr'));

    expect($hasEnglish)->not->toBeEmpty();
    expect($hasFrench)->not->toBeEmpty();
});

it('properly escapes xml special characters in urls', function () {
    $response = $this->get('/sitemap.xml');
    $xml = $response->getContent();

    $parsed = simplexml_load_string($xml);
    expect($parsed)->toBeInstanceOf(SimpleXMLElement::class);
});

it('does not include lastmod for homepage entries', function () {
    Feature::deactivate('blog');
    Feature::deactivate('page');
    Feature::deactivate('contact-form');
    Feature::deactivate('faq');
    Feature::deactivate('testimonials');

    $response = $this->get('/sitemap.xml');

    expect($response->getContent())->not->toContain('<lastmod>');
});

it('uses cache for subsequent requests', function () {
    BlogPost::factory()->for($this->language)->published()->create();

    $response1 = $this->get('/sitemap.xml');
    $etag1 = $response1->headers->get('ETag');

    $response2 = $this->get('/sitemap.xml');
    $etag2 = $response2->headers->get('ETag');

    expect($etag1)->toBe($etag2);
});
