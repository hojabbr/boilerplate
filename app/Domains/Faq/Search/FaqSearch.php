<?php

namespace App\Domains\Faq\Search;

use App\Core\Models\Language;
use App\Domains\Faq\Models\Faq;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FaqSearch
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

        $faqs = $this->searchFaqs($query, $languageId);

        return $faqs->map(function (Faq $faq) use ($urlPrefix) {
            return [
                'id' => $faq->id,
                'title' => Str::limit($faq->question, 80),
                'type' => 'faq',
                'url' => $urlPrefix.'/faq',
            ];
        })->values()->all();
    }

    /**
     * @return Collection<int, Faq>
     */
    private function searchFaqs(string $q, int $languageId): Collection
    {
        try {
            return Faq::search($q)
                ->where('language_id', $languageId)
                ->take(10)
                ->get();
        } catch (\Throwable) {
            $pattern = '%'.Str::lower($q).'%';

            return Faq::query()
                ->where('language_id', $languageId)
                ->where(function ($query) use ($pattern) {
                    $query->whereRaw('LOWER(question) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(answer) LIKE ?', [$pattern]);
                })
                ->orderBy('sort_order')
                ->take(10)
                ->get();
        }
    }
}
