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
    Feature::activate('contact-form');
    Feature::activate('faq');
    Feature::activate('testimonials');
});

it('generates entries with all features enabled', function () {
    BlogPost::factory()->for($this->language)->published()->create();
    Page::factory()->active()->create();
    Faq::factory()->for($this->language)->create();
    Testimonial::factory()->for($this->language)->create();

    $entries = (new SitemapGenerator)->generate();

    expect($entries)->not->toBeEmpty()->toBeArray();

    // Homepage uses /{locale} without trailing path
    $homepageEntries = array_filter($entries, fn (array $e) => preg_match('#/en$#', $e['loc']));
    expect($homepageEntries)->not->toBeEmpty();

    $blogEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/blog'));
    expect($blogEntries)->not->toBeEmpty();

    $pageEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/page/'));
    expect($pageEntries)->not->toBeEmpty();

    $faqEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/faq'));
    expect($faqEntries)->not->toBeEmpty();

    $testimonialEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/testimonials'));
    expect($testimonialEntries)->not->toBeEmpty();

    $contactEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/contact'));
    expect($contactEntries)->not->toBeEmpty();
});

it('excludes disabled features', function () {
    Feature::deactivate('blog');
    Feature::deactivate('faq');
    Feature::deactivate('testimonials');

    BlogPost::factory()->for($this->language)->published()->create();
    Page::factory()->active()->create();

    $entries = (new SitemapGenerator)->generate();

    $blogEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/blog'));
    expect($blogEntries)->toBeEmpty();

    $faqEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/faq'));
    expect($faqEntries)->toBeEmpty();

    $testimonialEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/testimonials'));
    expect($testimonialEntries)->toBeEmpty();

    $pageEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/page/'));
    expect($pageEntries)->not->toBeEmpty();

    $contactEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/contact'));
    expect($contactEntries)->not->toBeEmpty();
});

it('always includes homepage entries even with all features disabled', function () {
    Feature::deactivate('blog');
    Feature::deactivate('page');
    Feature::deactivate('contact-form');
    Feature::deactivate('faq');
    Feature::deactivate('testimonials');

    $entries = (new SitemapGenerator)->generate();

    expect($entries)->toBeArray();
    expect(count($entries))->toBeGreaterThanOrEqual(1);

    foreach ($entries as $entry) {
        expect($entry['priority'])->toBe(1.0);
        expect($entry['changefreq'])->toBe('daily');
    }
});

it('excludes soft deleted blog posts and pages', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();
    $page = Page::factory()->active()->create();

    $post->delete();
    $page->delete();

    $entries = (new SitemapGenerator)->generate();

    $postEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/blog/'.$post->slug));
    expect($postEntries)->toBeEmpty();

    $pageEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/page/'.$page->slug));
    expect($pageEntries)->toBeEmpty();
});

it('excludes unpublished blog posts', function () {
    $published = BlogPost::factory()->for($this->language)->published()->create();
    $draft = BlogPost::factory()->for($this->language)->unpublished()->create();

    $entries = (new SitemapGenerator)->generate();

    $publishedEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], $published->slug));
    expect($publishedEntries)->not->toBeEmpty();

    $draftEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], $draft->slug));
    expect($draftEntries)->toBeEmpty();
});

it('excludes future-dated blog posts', function () {
    $futurePost = BlogPost::factory()->for($this->language)->state([
        'published_at' => now()->addDays(7),
    ])->create();

    $entries = (new SitemapGenerator)->generate();

    $futureEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], $futurePost->slug));
    expect($futureEntries)->toBeEmpty();
});

it('excludes inactive pages', function () {
    $active = Page::factory()->active()->create();
    $inactive = Page::factory()->inactive()->create();

    $entries = (new SitemapGenerator)->generate();

    $activeEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/page/'.$active->slug));
    expect($activeEntries)->not->toBeEmpty();

    $inactiveEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/page/'.$inactive->slug));
    expect($inactiveEntries)->toBeEmpty();
});

it('includes all enabled locales', function () {
    $this->language->update(['is_enabled' => true]);
    Language::factory()->state(['code' => 'fr', 'is_enabled' => true])->create();

    $entries = (new SitemapGenerator)->generate();
    $urls = array_column($entries, 'loc');

    $hasEnglish = array_filter($urls, fn (string $url) => str_contains($url, '/en'));
    $hasFrench = array_filter($urls, fn (string $url) => str_contains($url, '/fr'));

    expect($hasEnglish)->not->toBeEmpty();
    expect($hasFrench)->not->toBeEmpty();
});

it('excludes disabled locales', function () {
    Language::factory()->state(['code' => 'de', 'is_enabled' => false])->create();

    $entries = (new SitemapGenerator)->generate();
    $urls = array_column($entries, 'loc');

    $hasGerman = array_filter($urls, fn (string $url) => str_contains($url, '/de/'));
    expect($hasGerman)->toBeEmpty();
});

it('handles locales without page translations gracefully', function () {
    $entries = (new SitemapGenerator)->generate();
    expect($entries)->toBeArray();
});

it('generates properly formatted entries', function () {
    BlogPost::factory()->for($this->language)->published()->create();
    Page::factory()->active()->create();

    $entries = (new SitemapGenerator)->generate();

    foreach ($entries as $entry) {
        expect($entry['loc'])
            ->toBeString()
            ->toMatch('/^https?:\/\//')
            ->not->toBeEmpty();

        expect($entry['changefreq'])->toBeIn(['daily', 'weekly', 'monthly']);

        expect($entry['priority'])
            ->toBeFloat()
            ->toBeGreaterThan(0)
            ->toBeLessThanOrEqual(1.0);
    }
});

it('includes lastmod for blog posts', function () {
    $post = BlogPost::factory()->for($this->language)->published()->create();

    $entries = (new SitemapGenerator)->generate();

    $postEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], $post->slug));
    expect($postEntries)->not->toBeEmpty();

    foreach ($postEntries as $entry) {
        expect($entry['lastmod'])->toMatch('/^\d{4}-\d{2}-\d{2}$/');
    }
});

it('sets null lastmod for index pages', function () {
    $entries = (new SitemapGenerator)->generate();

    $homepageEntries = array_filter($entries, fn (array $e) => $e['priority'] === 1.0);
    foreach ($homepageEntries as $entry) {
        expect($entry['lastmod'])->toBeNull();
    }
});

it('does not include faq entry when no faqs exist for locale', function () {
    $entries = (new SitemapGenerator)->generate();

    $faqEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/faq'));
    expect($faqEntries)->toBeEmpty();
});

it('does not include testimonials entry when no testimonials exist for locale', function () {
    $entries = (new SitemapGenerator)->generate();

    $testimonialEntries = array_filter($entries, fn (array $e) => str_contains($e['loc'], '/testimonials'));
    expect($testimonialEntries)->toBeEmpty();
});

it('uses correct priority hierarchy', function () {
    BlogPost::factory()->for($this->language)->published()->create();
    Page::factory()->active()->create();
    Faq::factory()->for($this->language)->create();
    Testimonial::factory()->for($this->language)->create();

    $entries = (new SitemapGenerator)->generate();

    $priorities = [];
    foreach ($entries as $entry) {
        if (preg_match('#/en$#', $entry['loc'])) {
            $priorities['homepage'] = $entry['priority'];
        } elseif (str_contains($entry['loc'], '/blog/')) {
            $priorities['blog_post'] = $entry['priority'];
        } elseif (str_contains($entry['loc'], '/en/blog')) {
            $priorities['blog_index'] = $entry['priority'];
        }
    }

    expect($priorities['homepage'])->toBeGreaterThan($priorities['blog_index']);
    expect($priorities['blog_index'])->toBeGreaterThan($priorities['blog_post']);
});
