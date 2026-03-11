<?php

namespace App\Core\Services;

use App\Core\Models\Language;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Faq\Models\Faq;
use App\Domains\Page\Models\Page;
use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use Laravel\Pennant\Feature;

class SitemapGenerator
{
    public const CACHE_KEY = 'sitemap.xml';

    /**
     * @var array<array{loc: string, lastmod: ?string, changefreq: string, priority: float}>
     */
    private array $entries = [];

    /**
     * @var array<int, Language>
     */
    private array $locales = [];

    public function __construct()
    {
        $this->locales = Language::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get()
            ->all();
    }

    /**
     * Generate all sitemap entries.
     *
     * @return array<array{loc: string, lastmod: ?string, changefreq: string, priority: float}>
     */
    public function generate(): array
    {
        $this->entries = [];

        $this->addHomepages();

        if (Feature::active('blog')) {
            $this->addBlogEntries();
        }

        if (Feature::active('page')) {
            $this->addPageEntries();
        }

        if (Feature::active('faq')) {
            $this->addFaqEntries();
        }

        if (Feature::active('testimonials')) {
            $this->addTestimonialEntries();
        }

        if (Feature::active('contact-form')) {
            $this->addContactEntries();
        }

        return $this->entries;
    }

    /**
     * Add homepage entries for each locale.
     */
    private function addHomepages(): void
    {
        foreach ($this->locales as $locale) {
            $url = $this->buildLocalizedUrl('/', $locale->code);
            $this->addEntry($url, null, 'daily', 1.0);
        }
    }

    /**
     * Add blog index and individual post entries.
     */
    private function addBlogEntries(): void
    {
        foreach ($this->locales as $locale) {
            $indexUrl = $this->buildLocalizedUrl('/blog', $locale->code);
            $this->addEntry($indexUrl, null, 'daily', 0.8);

            $posts = BlogPost::where('language_id', $locale->id)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->select(['id', 'slug', 'updated_at', 'created_at'])
                ->get();

            foreach ($posts as $post) {
                $postUrl = $this->buildLocalizedUrl("/blog/{$post->slug}", $locale->code);
                $lastmod = $post->updated_at?->format('Y-m-d') ?? $post->created_at?->format('Y-m-d');
                $this->addEntry($postUrl, $lastmod, 'weekly', 0.7);
            }
        }
    }

    /**
     * Add page entries.
     */
    private function addPageEntries(): void
    {
        foreach ($this->locales as $locale) {
            $pages = Page::where('is_active', true)
                ->select(['id', 'slug', 'updated_at', 'created_at', 'title'])
                ->get()
                ->filter(fn (Page $page) => $page->hasTranslation('title', $locale->code));

            foreach ($pages as $page) {
                $pageUrl = $this->buildLocalizedUrl("/page/{$page->slug}", $locale->code);
                $lastmod = $page->updated_at?->format('Y-m-d') ?? $page->created_at?->format('Y-m-d');
                $this->addEntry($pageUrl, $lastmod, 'monthly', 0.6);
            }
        }
    }

    /**
     * Add FAQ entry (single page per locale).
     */
    private function addFaqEntries(): void
    {
        foreach ($this->locales as $locale) {
            if (Faq::where('language_id', $locale->id)->exists()) {
                $faqUrl = $this->buildLocalizedUrl('/faq', $locale->code);
                $this->addEntry($faqUrl, null, 'monthly', 0.6);
            }
        }
    }

    /**
     * Add testimonials entry (single page per locale).
     */
    private function addTestimonialEntries(): void
    {
        foreach ($this->locales as $locale) {
            if (Testimonial::where('language_id', $locale->id)->exists()) {
                $testimonialUrl = $this->buildLocalizedUrl('/testimonials', $locale->code);
                $this->addEntry($testimonialUrl, null, 'monthly', 0.5);
            }
        }
    }

    /**
     * Add contact form entry (single page per locale).
     */
    private function addContactEntries(): void
    {
        foreach ($this->locales as $locale) {
            $contactUrl = $this->buildLocalizedUrl('/contact', $locale->code);
            $this->addEntry($contactUrl, null, 'monthly', 0.5);
        }
    }

    /**
     * Build a full localized URL for the given path and locale code.
     */
    private function buildLocalizedUrl(string $path, string $locale): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $path = ltrim($path, '/');

        if ($path === '') {
            return "{$baseUrl}/{$locale}";
        }

        return "{$baseUrl}/{$locale}/{$path}";
    }

    /**
     * Add an entry to the sitemap.
     */
    private function addEntry(string $loc, ?string $lastmod, string $changefreq, float $priority): void
    {
        if ($loc === '') {
            return;
        }

        $this->entries[] = [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    /**
     * Clear the sitemap cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<int, Language>
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return array<array{loc: string, lastmod: ?string, changefreq: string, priority: float}>
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
