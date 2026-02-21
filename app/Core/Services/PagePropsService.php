<?php

namespace App\Core\Services;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Models\Setting;
use Laravel\Pennant\Feature;

class PagePropsService implements PagePropsServiceInterface
{
    /**
     * Settings slice for Inertia page props (company_name, tagline, email, phone, social_links).
     * Uses app config fallbacks for company_name and tagline when empty.
     *
     * @return array{company_name: string, tagline: string, email: string|null, phone: string|null, social_links: array<string, mixed>}
     */
    public function settingsSlice(Setting $setting): array
    {
        $socialLinksRaw = $setting->getAttribute('social_links');
        /** @var array<string, mixed> $socialLinks */
        $socialLinks = is_array($socialLinksRaw) ? array_filter($socialLinksRaw) : [];

        return [
            'company_name' => $setting->company_name ?: config('app.name'),
            'tagline' => $setting->tagline ?: config('app.description'),
            'email' => $setting->email,
            'phone' => $setting->phone,
            'social_links' => $socialLinks,
        ];
    }

    /**
     * Feature flags array for Inertia page props (page, blog, contactForm).
     *
     * @return array{page: bool, blog: bool, contactForm: bool}
     */
    public function featuresArray(): array
    {
        return [
            'page' => Feature::active('page'),
            'blog' => Feature::active('blog'),
            'contactForm' => Feature::active('contact-form'),
        ];
    }
}
