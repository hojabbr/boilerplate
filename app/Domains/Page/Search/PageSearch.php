<?php

namespace App\Domains\Page\Search;

use App\Domains\Page\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PageSearch
{
    /**
     * @return Collection<int, Page>
     */
    public function search(string $query): Collection
    {
        try {
            return Page::search($query)->take(10)->get();
        } catch (\Throwable) {
            $pattern = '%'.Str::lower($query).'%';

            return Page::query()
                ->whereRaw('LOWER(slug) LIKE ?', [$pattern])
                ->take(10)
                ->get();
        }
    }

    /**
     * @return array<int, array{id: int, title: string, slug: string, type: string, url: string}>
     */
    public function searchAndFormat(string $query, string $locale, string $urlPrefix): array
    {
        $pageModels = $this->search($query);

        return $pageModels->map(function (Page $page) use ($locale, $urlPrefix) {
            $title = $page->getTranslation('title', $locale)
                ?: $page->getTranslation('title', config('app.fallback_locale', 'en'));

            return [
                'id' => $page->id,
                'title' => $title,
                'slug' => $page->slug,
                'type' => 'page',
                'url' => "{$urlPrefix}/page/{$page->slug}",
            ];
        })->values()->all();
    }
}
