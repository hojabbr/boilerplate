<?php

namespace App\Domains\Testimonial\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Http\Controllers\Controller;
use App\Core\Models\Setting;
use App\Domains\Testimonial\Services\TestimonialService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TestimonialController extends Controller
{
    public function show(Request $request, TestimonialService $testimonialService, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        if (! Feature::active('testimonials')) {
            abort(404);
        }

        $locale = app()->getLocale();
        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);
        $features = $pageProps->featuresArray();
        $items = $testimonialService->getItemsForLocale($locale);

        return Inertia::render('testimonials/Show', [
            'settings' => $settings,
            'features' => $features,
            'seo' => [
                'title' => __('What our customers say'),
                'description' => __('Read testimonials from our customers and partners.'),
            ],
            'items' => $items,
        ]);
    }
}
