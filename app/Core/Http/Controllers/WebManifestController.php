<?php

namespace App\Core\Http\Controllers;

use App\Core\Models\Setting;
use Illuminate\Http\JsonResponse;

class WebManifestController extends Controller
{
    /**
     * Serve a dynamic site.webmanifest using company name and uploaded branding media.
     */
    public function __invoke(): JsonResponse
    {
        $setting = Setting::site();
        $name = $setting->company_name ?: config('app.name');

        $icon192 = $setting->getFirstMediaUrl('manifest_icon_192') ?: asset('favicon/web-app-manifest-192x192.png');
        $icon512 = $setting->getFirstMediaUrl('manifest_icon_512') ?: asset('favicon/web-app-manifest-512x512.png');

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'icons' => [
                [
                    'src' => $icon192,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
                [
                    'src' => $icon512,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ])->header('Content-Type', 'application/manifest+json');
    }
}
