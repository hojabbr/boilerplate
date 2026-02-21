<?php

namespace App\Domains\Landing\Services;

use App\Domains\Blog\Models\BlogPost;
use App\Domains\Landing\DTOs\LandingSectionDto;
use App\Domains\Landing\Models\LandingSection;
use Illuminate\Support\Facades\Cache;
use Laravel\Pennant\Feature;

class LandingService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSectionsForLocale(string $locale): array
    {
        $ttl = config('cache.content_ttl', 86400);

        return Cache::remember("landing_sections.{$locale}", $ttl, function (): array {
            return LandingSection::query()
                ->active()
                ->ordered()
                ->with(['items' => fn ($q) => $q->orderBy('sort_order')->with('media')])
                ->with('media')
                ->get()
                ->map(fn (LandingSection $section) => LandingSectionDto::fromModel($section)->toArray())
                ->values()
                ->all();
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLatestPosts(string $locale, int $limit = 3): array
    {
        if (! Feature::active('blog')) {
            return [];
        }

        return BlogPost::query()
            ->byLocale($locale)
            ->published()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(function (BlogPost $post): array {
                $firstImage = $post->getFirstMedia('gallery');
                $publishedAt = $post->published_at;

                return [
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'published_at' => $publishedAt instanceof \DateTimeInterface ? $publishedAt->format('c') : null,
                    'thumbnail_url' => $firstImage?->getUrl('card'),
                ];
            })
            ->values()
            ->all();
    }
}
