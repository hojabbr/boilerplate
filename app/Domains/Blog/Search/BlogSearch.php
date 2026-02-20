<?php

namespace App\Domains\Blog\Search;

use App\Core\Models\Language;
use App\Domains\Blog\Models\BlogPost;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlogSearch
{
    /**
     * @return Collection<int, array{id: int, title: string, slug: string, type: string, url: string}>
     */
    public function search(string $query, string $locale, string $urlPrefix): Collection
    {
        $languageId = Language::where('code', $locale)->value('id');
        if (! $languageId) {
            return collect();
        }

        $posts = $this->searchPosts($query, $languageId);

        return $posts->map(function (BlogPost $post) use ($urlPrefix) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'type' => 'post',
                'url' => "{$urlPrefix}/blog/{$post->slug}",
            ];
        })->values();
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function searchPosts(string $q, int $languageId): Collection
    {
        try {
            $posts = BlogPost::search($q)
                ->where('language_id', $languageId)
                ->take(15)
                ->get();
        } catch (\Throwable) {
            $pattern = '%'.Str::lower($q).'%';
            $posts = BlogPost::query()
                ->where('language_id', $languageId)
                ->where(function ($query) use ($pattern) {
                    $query->whereRaw('LOWER(slug) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(title) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(body) LIKE ?', [$pattern]);
                })
                ->take(15)
                ->get();
        }

        return $posts->filter(function (BlogPost $post): bool {
            $at = $post->published_at;

            return $at instanceof \DateTimeInterface && $at->getTimestamp() <= time();
        })
            ->take(10)
            ->values();
    }
}
