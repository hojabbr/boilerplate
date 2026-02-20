<?php

namespace App\Domains\Search\Http\Controllers;

use App\Domains\Blog\Search\BlogSearch;
use App\Domains\Pages\Search\PageSearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search pages and blog posts via Laravel Scout (Meilisearch / database / collection).
     * Returns JSON for the nav search bar.
     */
    public function __invoke(Request $request, PageSearch $pageSearch, BlogSearch $blogSearch): JsonResponse
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

        $pages = $pageSearch->searchAndFormat($q, $locale, $prefix);
        $blogPosts = $blogSearch->search($q, $locale, $prefix)->all();

        return response()->json([
            'pages' => $pages,
            'blog_posts' => $blogPosts,
        ]);
    }
}
