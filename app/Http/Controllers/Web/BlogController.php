<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BlogController extends Controller
{
    public function index(Request $request): Response|HttpResponse
    {
        if (! Feature::active('blog')) {
            abort(404);
        }

        /** @var array<int, array{slug: string, title: string, excerpt: string, published_at: string|null, thumbnail_url: string|null}> $posts */
        $posts = BlogPost::query()
            ->byLocale(app()->getLocale())
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(function (BlogPost $post): array {
                $firstImage = $post->getFirstMedia('gallery');
                $publishedAt = $post->published_at;

                return [
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'published_at' => $publishedAt instanceof \DateTimeInterface ? $publishedAt->format('c') : null,
                    'thumbnail_url' => $firstImage?->getUrl('thumb'),
                ];
            })
            ->values()
            ->all();

        $setting = Setting::site();

        return Inertia::render('blog/Index', [
            'posts' => $posts,
            'settings' => [
                'company_name' => $setting->company_name,
                'tagline' => $setting->tagline,
                'email' => $setting->email,
                'phone' => $setting->phone,
            ],
            'features' => [
                'pages' => Feature::active('pages'),
                'blog' => Feature::active('blog'),
                'contactForm' => Feature::active('contact-form'),
            ],
        ]);
    }

    public function show(Request $request, string $slug): Response|HttpResponse
    {
        if (! Feature::active('blog')) {
            abort(404);
        }

        $post = BlogPost::query()
            ->byLocale(app()->getLocale())
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $setting = Setting::site();

        $gallery = $post->getMedia('gallery')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'full_url' => $media->getUrl('full'),
            'thumb_url' => $media->getUrl('thumb'),
            'type' => 'image',
            'alt' => $media->getCustomProperty('alt'),
            'title' => $media->getCustomProperty('title') ?? $media->name,
        ])->values()->all();

        $videos = $post->getMedia('videos')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'type' => 'video',
            'mime_type' => $media->mime_type,
        ])->values()->all();

        $documents = $post->getMedia('documents')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'file_name' => $media->file_name,
            'type' => 'file',
        ])->values()->all();

        return Inertia::render('blog/Show', [
            'post' => [
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'meta_description' => $post->meta_description,
                'published_at' => $post->published_at instanceof \DateTimeInterface ? $post->published_at->format('c') : null,
                'gallery' => $gallery,
                'videos' => $videos,
                'documents' => $documents,
            ],
            'settings' => [
                'company_name' => $setting->company_name,
                'tagline' => $setting->tagline,
                'email' => $setting->email,
                'phone' => $setting->phone,
            ],
            'features' => [
                'pages' => Feature::active('pages'),
                'blog' => Feature::active('blog'),
                'contactForm' => Feature::active('contact-form'),
            ],
        ]);
    }
}
