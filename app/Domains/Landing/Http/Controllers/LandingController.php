<?php

namespace App\Domains\Landing\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Http\Controllers\Controller;
use App\Core\Models\Setting;
use App\Domains\Landing\Services\LandingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;

class LandingController extends Controller
{
    public function __invoke(Request $request, LandingService $landingService, PagePropsServiceInterface $pageProps): Response
    {
        $setting = Setting::site();
        $companyName = $setting->company_name ?: config('app.name');
        $locale = app()->getLocale();

        $sections = $landingService->getSectionsForLocale($locale);
        $latestPosts = $landingService->getLatestPosts($locale, 3);

        $settings = $pageProps->settingsSlice($setting);

        return Inertia::render('welcome', [
            'canRegister' => Feature::for(null)->active('registration'),
            'settings' => $settings,
            'features' => $pageProps->featuresArray(),
            'seo' => [
                'title' => __('Welcome').' - '.$companyName,
                'description' => $settings['tagline'],
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
