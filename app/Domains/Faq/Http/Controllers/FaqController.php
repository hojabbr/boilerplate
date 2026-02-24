<?php

namespace App\Domains\Faq\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Http\Controllers\Controller;
use App\Core\Models\Setting;
use App\Domains\Faq\Services\FaqService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class FaqController extends Controller
{
    public function show(Request $request, FaqService $faqService, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        if (! Feature::active('faq')) {
            abort(404);
        }

        $locale = app()->getLocale();
        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);
        $features = $pageProps->featuresArray();
        $items = $faqService->getItemsForLocale($locale);

        return Inertia::render('faq/Show', [
            'settings' => $settings,
            'features' => $features,
            'seo' => [
                'title' => __('Frequently asked questions'),
                'description' => __('Find answers to common questions about our product and services.'),
            ],
            'items' => $items,
        ]);
    }
}
