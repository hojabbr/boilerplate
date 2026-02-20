<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class LandingController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $setting = Setting::site();

        $companyName = $setting->company_name ?: __('common.app_fallback');

        return Inertia::render('welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'settings' => [
                'company_name' => $setting->company_name,
                'tagline' => $setting->tagline,
                'email' => $setting->email,
                'phone' => $setting->phone,
            ],
            'features' => [
                'pages' => \Laravel\Pennant\Feature::active('pages'),
                'blog' => \Laravel\Pennant\Feature::active('blog'),
                'contactForm' => \Laravel\Pennant\Feature::active('contact-form'),
            ],
            'seo' => [
                'title' => __('Welcome'),
                'description' => $setting->tagline ?: __('welcome.tagline_fallback'),
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
        ]);
    }
}
