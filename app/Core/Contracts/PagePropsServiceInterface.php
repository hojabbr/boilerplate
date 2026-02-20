<?php

namespace App\Core\Contracts;

use App\Core\Models\Setting;

interface PagePropsServiceInterface
{
    /**
     * Settings slice for Inertia page props.
     *
     * @return array{company_name: string, tagline: string, email: string|null, phone: string|null, social_links: array<string, mixed>}
     */
    public function settingsSlice(Setting $setting): array;

    /**
     * Feature flags array for Inertia page props.
     *
     * @return array{pages: bool, blog: bool, contactForm: bool}
     */
    public function featuresArray(): array;
}
