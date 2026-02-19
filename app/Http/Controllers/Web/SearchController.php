<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Search pages and blog posts via Laravel Scout (Meilisearch / database / collection).
     * Returns JSON for the nav search bar.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $q = is_string($q) ? trim($q) : '';

        if ($q === '') {
            return response()->json([
                'pages' => [],
                'blog_posts' => [],
            ]);
        }

        $locale = app()->getLocale();
        $prefix = $locale ? "/{$locale}" : '';

        $pageModels = $this->searchPages($q);
        $pages = $pageModels->map(function (Page $page) use ($prefix) {
            $title = $page->getTranslation('title', app()->getLocale())
                ?: $page->getTranslation('title', config('app.fallback_locale', 'en'));

            return [
                'id' => $page->id,
                'title' => $title,
                'slug' => $page->slug,
                'type' => 'page',
                'url' => "{$prefix}/page/{$page->slug}",
            ];
        })->values()->all();

        $languageId = Language::where('code', $locale)->value('id');
        $posts = $this->searchBlogPosts($q, $languageId);
        $blogPosts = $posts->map(function (BlogPost $post) use ($prefix) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'type' => 'post',
                'url' => "{$prefix}/blog/{$post->slug}",
            ];
        })->values()->all();

        return response()->json([
            'pages' => $pages,
            'blog_posts' => $blogPosts,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Page>
     */
    private function searchPages(string $q): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Page::search($q)->take(10)->get();
        } catch (\Throwable) {
            $pattern = '%'.Str::lower($q).'%';

            return Page::query()
                ->whereRaw('LOWER(slug) LIKE ?', [$pattern])
                ->take(10)
                ->get();
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, BlogPost>
     */
    private function searchBlogPosts(string $q, ?int $languageId): \Illuminate\Support\Collection
    {
        if (! $languageId) {
            return collect();
        }

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
