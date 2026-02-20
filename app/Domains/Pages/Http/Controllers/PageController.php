<?php

namespace App\Domains\Pages\Http\Controllers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Models\Setting;
use App\Domains\Pages\Queries\GetPageBySlug;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PageController extends Controller
{
    public function show(Request $request, string $slug, GetPageBySlug $query, PagePropsServiceInterface $pageProps): Response|HttpResponse
    {
        if (! Feature::active('pages')) {
            abort(404);
        }

        $result = $query->handle($slug);
        if (! $result) {
            abort(404);
        }

        $setting = Setting::site();
        $settings = $pageProps->settingsSlice($setting);

        return Inertia::render('pages/Show', [
            'page' => $result['page'],
            'seo' => [
                'title' => ($result['page']['meta_title'] ?: $result['page']['title']).' - '.$settings['company_name'],
                'description' => $result['page']['meta_description'] ?: $settings['tagline'],
                'image' => $result['og_image'],
            ],
            'settings' => $settings,
            'features' => $pageProps->featuresArray(),
        ]);
    }
}
