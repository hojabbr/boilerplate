<?php

namespace App\Http\Controllers\Web;

use App\Data\DTOs\LandingSectionDto;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\LandingSection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Laravel\Pennant\Feature;

class LandingController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $setting = Setting::site();

        $companyName = $setting->company_name ?: config('app.name');

        $ttl = config('cache.content_ttl', 86400);
        $locale = app()->getLocale();
        $sections = Cache::remember("landing_sections.{$locale}", $ttl, function (): array {
            return LandingSection::query()
                ->active()
                ->ordered()
                ->with(['items' => fn ($q) => $q->orderBy('sort_order')->with('media')])
                ->with('media')
                ->get()
                ->map(fn (LandingSection $section) => LandingSectionDto::fromModel($section)->toArray())
                ->values()
                ->all();
        });

        $hasLatestPostsSection = collect($sections)->contains('type', 'latest_posts');
        $latestPosts = [];
        if ($hasLatestPostsSection && Feature::active('blog')) {
            $latestPosts = BlogPost::query()
                ->byLocale($locale)
                ->published()
                ->orderByDesc('published_at')
                ->limit(3)
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
        }

        $socialLinksRaw = $setting->getAttribute('social_links');
        /** @var array<string, mixed> $socialLinks */
        $socialLinks = is_array($socialLinksRaw) ? array_filter($socialLinksRaw) : [];

        return Inertia::render('welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'settings' => [
                'company_name' => $setting->company_name ?: config('app.name'),
                'tagline' => $setting->tagline ?: config('app.description'),
                'email' => $setting->email,
                'phone' => $setting->phone,
                'social_links' => $socialLinks,
            ],
            'features' => [
                'pages' => \Laravel\Pennant\Feature::active('pages'),
                'blog' => \Laravel\Pennant\Feature::active('blog'),
                'contactForm' => \Laravel\Pennant\Feature::active('contact-form'),
            ],
            'seo' => [
                'title' => __('Welcome').' - '.$companyName,
                'description' => $setting->tagline ?: config('app.description'),
            ],
            'messages' => [
                'heading' => __('welcome.heading', ['name' => $companyName]),
                'tagline_fallback' => __('welcome.tagline_fallback'),
                'cta_get_started' => __('welcome.cta_get_started'),
                'cta_contact_us' => __('welcome.cta_contact_us'),
                'explore' => __('welcome.explore'),
                'about_us_title' => __('welcome.about_us_title'),
                'about_us_description' => __('welcome.about_us_description'),
                'blog_title' => __('welcome.blog_title'),
                'blog_description' => __('welcome.blog_description'),
                'contact_title' => __('welcome.contact_title'),
                'contact_description' => __('welcome.contact_description'),
            ],
            'sections' => $sections,
            'latest_posts' => $latestPosts,
        ]);
    }
}
