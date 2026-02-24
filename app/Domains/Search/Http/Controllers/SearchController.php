<?php

namespace App\Domains\Search\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Domains\Blog\Search\BlogSearch;
use App\Domains\Faq\Search\FaqSearch;
use App\Domains\Page\Search\PageSearch;
use App\Domains\Testimonial\Search\TestimonialSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;

class SearchController extends Controller
{
    /**
     * Search pages, blog posts, FAQs, and testimonials via Laravel Scout (Meilisearch / database / collection).
     * Returns JSON for the nav search bar.
     */
    public function __invoke(
        Request $request,
        PageSearch $pageSearch,
        BlogSearch $blogSearch,
        FaqSearch $faqSearch,
        TestimonialSearch $testimonialSearch
    ): JsonResponse {
        $q = $request->query('q', '');
        $q = is_string($q) ? trim($q) : '';

        if ($q === '') {
            return response()->json([
                'pages' => [],
                'blog_posts' => [],
                'faqs' => [],
                'testimonials' => [],
            ]);
        }

        $locale = app()->getLocale();
        $prefix = $locale ? "/{$locale}" : '';

        $pages = Feature::active('page') ? $pageSearch->searchAndFormat($q, $locale, $prefix) : [];
        $blogPosts = Feature::active('blog') ? $blogSearch->search($q, $locale, $prefix)->all() : [];
        $faqs = Feature::active('faq') ? $faqSearch->searchAndFormat($q, $locale, $prefix) : [];
        $testimonials = Feature::active('testimonials') ? $testimonialSearch->searchAndFormat($q, $locale, $prefix) : [];

        return response()->json([
            'pages' => $pages,
            'blog_posts' => $blogPosts,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
        ]);
    }
}
