<?php

namespace App\Observers;

use App\Models\LandingSectionItem;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates landing sections cache for all CRUD operations (create/update/delete/restore/force delete).
 * Section items are part of the landing response; any change must invalidate cache.
 */
class LandingSectionItemObserver
{
    /**
     * Invalidate landing sections cache on create or update.
     */
    public function saved(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on delete (soft or force).
     */
    public function deleted(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on restore.
     */
    public function restored(LandingSectionItem $landingSectionItem): void
    {
        $this->forgetLandingSectionsCache();
    }

    /**
     * Invalidate landing sections cache on force delete.
     */
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
