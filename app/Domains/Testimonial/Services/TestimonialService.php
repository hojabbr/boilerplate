<?php

namespace App\Domains\Testimonial\Services;

use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class TestimonialService
{
    /**
     * @return array<int, array{quote: string, author: string, role: string|null}>
     */
    public function getItemsForLocale(string $locale): array
    {
        $ttl = config('cache.content_ttl', 86400);

        return Cache::remember(Testimonial::listCacheKey($locale), $ttl, function () use ($locale): array {
            return Testimonial::query()
                ->byLocale($locale)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (Testimonial $t): array => [
                    'quote' => $t->quote,
                    'author' => $t->author,
                    'role' => $t->role,
                ])
                ->values()
                ->all();
        });
    }
}
