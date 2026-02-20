<?php

namespace App\Domains\Blog\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Models\Setting;
use App\Domains\Blog\Queries\GetPostBySlug;
use App\Domains\Blog\Queries\GetPublishedPosts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BlogController extends Controller
{
    public function index(Request $request, GetPublishedPosts $query, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        $posts = $query->handle();
        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);

        return Inertia::render('blog/Index', [
            'posts' => $posts,
            'settings' => $settings,
            'features' => $pageProps->featuresArray(),
            'seo' => [
                'title' => __('Blog').' - '.$settings['company_name'],
                'description' => $settings['tagline'] ?: __('Our latest news and articles.'),
            ],
            'messages' => [
                'title' => __('blog.title'),
                'no_posts' => __('blog.no_posts'),
            ],
        ]);
    }

    public function show(Request $request, string $slug, GetPostBySlug $query, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        $result = $query->handle($slug);
        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);

        return Inertia::render('blog/Show', [
            'post' => $result['post'],
            'seo' => [
                'title' => $result['post']['title'].' - '.$settings['company_name'],
                'description' => $result['post']['meta_description'] ?: $result['post']['excerpt'],
                'image' => $result['og_image'],
                'type' => 'article',
            ],
            'messages' => [
                'media_gallery' => __('blog.media_gallery'),
                'documents' => __('blog.documents'),
            ],
            'settings' => $settings,
            'features' => $pageProps->featuresArray(),
        ]);
    }
}
