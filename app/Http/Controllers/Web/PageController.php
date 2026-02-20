<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PageController extends Controller
{
    public function show(Request $request, string $slug): Response|HttpResponse
    {
        if (! Feature::active('pages')) {
            abort(404);
        }

        $page = Page::query()->where('slug', $slug)->first();

        if (! $page) {
            abort(404);
        }

        $setting = Setting::site();

        $gallery = $page->getMedia('gallery')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'full_url' => $media->getUrl('full'),
            'thumb_url' => $media->getUrl('thumb'),
        ])->values()->all();

        $firstImage = $page->getMedia('gallery')->first();

        return Inertia::render('pages/Show', [
            'page' => [
                'title' => $page->title,
                'body' => $page->body,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'gallery' => $gallery,
            ],
            'seo' => [
                'title' => $page->meta_title ?: $page->title,
                'description' => $page->meta_description,
                'image' => $firstImage?->getUrl('full'),
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
