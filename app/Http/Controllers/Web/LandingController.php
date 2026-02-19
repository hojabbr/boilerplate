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
        ]);
    }
}
