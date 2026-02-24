<?php

namespace App\Domains\Faq\Services;

use App\Domains\Faq\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqService
{
    /**
     * @return array<int, array{question: string, answer: string}>
     */
    public function getItemsForLocale(string $locale): array
    {
        $ttl = config('cache.content_ttl', 86400);

        return Cache::remember(Faq::listCacheKey($locale), $ttl, function () use ($locale): array {
            return Faq::query()
                ->byLocale($locale)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (Faq $faq): array => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])
                ->values()
                ->all();
        });
    }
}
