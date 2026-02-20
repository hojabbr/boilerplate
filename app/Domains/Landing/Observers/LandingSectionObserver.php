<?php

namespace App\Domains\Landing\Observers;

use App\Domains\Landing\Models\LandingSection;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates landing sections cache for all CRUD operations (create/update/delete/restore/force delete).
 * Ensures changes in Filament or elsewhere are reflected on the next request.
 */
class LandingSectionObserver
{
    public function saved(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function deleted(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function restored(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function forceDeleted(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    private function forgetLandingSectionsCache(): void
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        foreach ($locales as $locale) {
            Cache::forget("landing_sections.{$locale}");
        }
    }
}
