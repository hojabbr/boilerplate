<?php

namespace App\Domains\Landing\Observers;

use App\Domains\Landing\Models\LandingSectionItem;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates landing sections cache for all CRUD operations.
 * Section items are part of the landing response; any change must invalidate cache.
 */
class LandingSectionItemObserver
{
    public function saved(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function deleted(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function restored(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    public function forceDeleted(LandingSectionItem $landingSectionItem): void
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
