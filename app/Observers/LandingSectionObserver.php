<?php

namespace App\Observers;

use App\Models\LandingSection;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates landing sections cache for all CRUD operations (create/update/delete/restore/force delete).
 * Ensures changes in Filament or elsewhere are reflected on the next request.
 */
class LandingSectionObserver
{
    /**
     * Invalidate landing sections cache on create or update.
     */
    public function saved(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on delete (soft or force).
     */
    public function deleted(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on restore.
     */
    public function restored(LandingSection $landingSection): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on force delete.
     */
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
