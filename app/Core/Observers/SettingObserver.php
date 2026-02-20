<?php

namespace App\Core\Observers;

use App\Core\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates site settings cache for all CRUD operations on the site setting.
 * Ensures changes in Filament or elsewhere are reflected on the next request.
 */
class SettingObserver
{
    /**
     * Invalidate cache when key is changed from 'site' (old key was site).
     */
    public function updating(Setting $setting): void
    {
        if ($setting->getOriginal('key') === 'site') {
            Cache::forget(Setting::siteCacheKey());
        }
    }

    /**
     * Invalidate site settings cache on create or update.
     */
    public function saved(Setting $setting): void
    {
        if ($setting->key === 'site') {
            Cache::forget(Setting::siteCacheKey());
        }
    }

    /**
     * Invalidate site settings cache on delete.
     */
    public function deleted(Setting $setting): void
    {
        if ($setting->key === 'site') {
            Cache::forget(Setting::siteCacheKey());
        }
    }
}
