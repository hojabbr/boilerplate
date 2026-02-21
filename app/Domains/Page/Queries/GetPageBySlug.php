<?php

namespace App\Domains\Page\Queries;

use App\Domains\Page\Models\Page;
use Illuminate\Support\Facades\Cache;

class GetPageBySlug
{
    /**
     * @return array{page: array<string, mixed>, gallery: array<int, array<string, mixed>>, og_image: string|null}|null
     */
    public function handle(string $slug): ?array
    {
        $ttl = config('cache.content_ttl', 86400);
        $page = Cache::remember(Page::slugCacheKey($slug), $ttl, fn (): ?Page => Page::query()->active()->where('slug', $slug)->first());

        if (! $page) {
            return null;
        }

        $gallery = $page->getMedia('gallery')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'full_url' => $media->getUrl('full'),
            'thumb_url' => $media->getUrl('thumb'),
        ])->values()->all();

        $firstImage = $page->getMedia('gallery')->first();

        return [
            'page' => [
                'title' => $page->title,
                'body' => $page->body,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'gallery' => $gallery,
            ],
            'gallery' => $gallery,
            'og_image' => $firstImage?->getUrl('full'),
        ];
    }
}
