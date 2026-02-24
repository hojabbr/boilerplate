<?php

namespace App\Domains\Testimonial\Search;

use App\Core\Models\Language;
use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TestimonialSearch
{
    /**
     * @return array<int, array{id: int, title: string, type: string, url: string}>
     */
    public function searchAndFormat(string $query, string $locale, string $urlPrefix): array
    {
        $languageId = Language::where('code', $locale)->value('id');
        if (! $languageId) {
            return [];
        }

        $testimonials = $this->searchTestimonials($query, $languageId);

        return $testimonials->map(function (Testimonial $t) use ($urlPrefix) {
            return [
                'id' => $t->id,
                'title' => Str::limit($t->quote, 80),
                'type' => 'testimonial',
                'url' => $urlPrefix.'/testimonials',
            ];
        })->values()->all();
    }

    /**
     * @return Collection<int, Testimonial>
     */
    private function searchTestimonials(string $q, int $languageId): Collection
    {
        try {
            return Testimonial::search($q)
                ->where('language_id', $languageId)
                ->take(10)
                ->get();
        } catch (\Throwable) {
            $pattern = '%'.Str::lower($q).'%';

            return Testimonial::query()
                ->where('language_id', $languageId)
                ->where(function ($query) use ($pattern) {
                    $query->whereRaw('LOWER(quote) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(author) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(role) LIKE ?', [$pattern]);
                })
                ->orderBy('sort_order')
                ->take(10)
                ->get();
        }
    }
}
