---
title: Sitemap
layout: default
parent: Features
nav_order: 5
description: 'Dynamic XML sitemap with feature flags and multi-locale support.'
---

# Sitemap

The boilerplate includes a fully dynamic XML sitemap that automatically respects feature flags and includes all active locales. The sitemap is cached and automatically invalidated when content changes.

## Overview

The sitemap is served at `/sitemap.xml` and includes:

- **Homepages** — One entry per enabled locale
- **Blog** — Index page + individual published posts (if blog feature is enabled)
- **Pages** — All active static pages in each locale (if page feature is enabled)
- **FAQ** — Single page per locale (if FAQ feature is enabled and FAQs exist)
- **Testimonials** — Single page per locale (if testimonials feature is enabled and testimonials exist)
- **Contact form** — Single page per locale (if contact-form feature is enabled)

## Features

### Feature Flag Aware

The sitemap respects all Laravel Pennant feature flags. Disabled features are automatically excluded from the sitemap:

```
- blog → /blog, /blog/{slug}
- page → /page/{slug}
- faq → /faq
- testimonials → /testimonials
- contact-form → /contact
```

Toggle features in **Filament → Settings → Feature flags** to control sitemap content.

### Multi-Locale Support

The sitemap automatically includes all enabled locales from your Languages table. Each content entry is generated for every locale:

```xml
<url>
  <loc>http://example.com/en/blog/post-slug</loc>
  ...
</url>
<url>
  <loc>http://example.com/de/blog/post-slug</loc>
  ...
</url>
```

Languages can be managed in **Filament → Settings → Languages**.

### Intelligent Content Filtering

The sitemap automatically excludes:

- **Soft-deleted records** — Posts, pages, FAQs, testimonials
- **Unpublished blog posts** — Only published posts with `published_at <= now()` included
- **Inactive pages** — Only pages with `is_active = true` included
- **Missing translations** — Pages without translations in a locale are skipped

### Performance & Caching

The sitemap is cached perpetually with automatic invalidation:

- **Cached key:** `sitemap.xml` in your default cache store
- **Invalidation:** Cache clears automatically when:
    - Blog posts are created, updated, deleted, or restored
    - Pages are created, updated, deleted, or restored
    - FAQs are created, deleted, or restored
    - Testimonials are created, deleted, or restored

The first request after creation/change regenerates the sitemap; subsequent requests serve from cache.

## Usage

### Access the Sitemap

Simply visit:

```
http://your-domain.com/sitemap.xml
```

### Submit to Search Engines

**Google Search Console:**

1. Go to [https://search.google.com/search-console](https://search.google.com/search-console)
2. Select your property
3. Go to **Sitemaps** (left sidebar)
4. Add: `https://your-domain.com/sitemap.xml`

**Bing Webmaster Tools:**

1. Go to [https://www.bing.com/webmasters](https://www.bing.com/webmasters)
2. Select your site
3. Go to **Sitemaps** → **Submit sitemap**
4. Add: `https://your-domain.com/sitemap.xml`

### Update robots.txt

Optionally link the sitemap in `public/robots.txt`:

```
User-agent: *
Allow: /

Sitemap: https://your-domain.com/sitemap.xml
```

## Implementation

The sitemap is built with a service-oriented architecture:

### Files

- **[SitemapGenerator](../app/Core/Services/SitemapGenerator.php)** — Service that builds sitemap entries by checking feature flags, fetching content, and respecting filters
- **[SitemapController](../app/Domains/Sitemap/Http/Controllers/SitemapController.php)** — HTTP handler that generates XML response and manages caching
- **[SitemapCacheObserver](../app/Core/Observers/SitemapCacheObserver.php)** — Observer that hooks into model lifecycle events to invalidate cache
- **Route:** Registered at `GET /sitemap.xml` in [routes/web.php](../routes/web.php)

### Priority & Changefreq

The sitemap uses semantic SEO values:

| Content      | Priority | Changefreq |
| ------------ | -------- | ---------- |
| Homepage     | 1.0      | daily      |
| Blog index   | 0.8      | daily      |
| Blog posts   | 0.7      | weekly     |
| Pages        | 0.6      | monthly    |
| Contact form | 0.8      | daily      |
| FAQ          | 0.6      | monthly    |
| Testimonials | 0.6      | monthly    |

## Testing

Run the sitemap tests:

```bash
# Unit tests
vendor/bin/sail artisan test tests/Unit/Services/SitemapGeneratorTest.php

# Feature tests (HTTP response)
vendor/bin/sail artisan test tests/Feature/Http/SitemapControllerTest.php

# Cache invalidation tests
vendor/bin/sail artisan test tests/Feature/Services/SitemapCacheInvalidationTest.php

# All sitemap tests
vendor/bin/sail artisan test --filter=Sitemap
```

## Customization

### Modify Priority or Changefreq

Edit [app/Core/Services/SitemapGenerator.php](../app/Core/Services/SitemapGenerator.php) and adjust the `addEntry()` calls for each content type:

```php
// Example: increase blog post priority
$this->addEntry($postUrl, $lastmod, 'weekly', 0.8); // was 0.7
```

### Add New Content Type

1. Create a new method in `SitemapGenerator`:

```php
private function addCustomEntries(): void
{
    foreach ($this->locales as $locale) {
        // Your logic here
        $url = $this->buildLocalizedUrl('/custom', $locale->code);
        $this->addEntry($url, null, 'daily', 0.7);
    }
}
```

2. Call it in `generate()`:

```php
if (Feature::active('custom-feature')) {
    $this->addCustomEntries();
}
```

3. Register a feature flag if needed:

```php
Feature::define('custom-feature', CustomFeature::class);
```

### Disable Caching

Change `Cache::rememberForever()` to direct generation in [SitemapController](../app/Domains/Sitemap/Http/Controllers/SitemapController.php):

```php
$entries = $generator->generate(); // Always fresh
```

## Common Issues

**Sitemap shows no entries**

- Check that at least one feature flag is enabled in Filament → Settings → Feature flags
- Verify you have content (blog posts, pages, etc.)
- Check that content is active/published

**Sitemap not updating after content changes**

- Feature flag toggles may not clear cache automatically (design choice for performance)
- Manually clear cache: `vendor/bin/sail artisan cache:clear`

**URLs have wrong locale**

- Verify locales are enabled in Filament → Settings → Languages
- Check `is_enabled` flag on Language models

## See Also

- [Localization](localization.md) — Route prefixes and locale handling
- [Feature Flags](feature-flags.md) — Laravel Pennant configuration
- [Search](search.md) — Full-text search with Scout
