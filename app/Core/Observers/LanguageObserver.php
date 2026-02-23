<?php

namespace App\Core\Observers;

use App\Core\Models\Language;
use App\Core\Services\SupportedLocalesService;
use Illuminate\Support\Facades\Cache;

/**
 * Cascades soft delete/restore to related pages when a language is soft-deleted or restored.
 * BlogPosts are cascade-deleted by DB FK when a language is force-deleted.
 * Invalidates supported_locales cache so next request gets DB-driven config.
 * Fires for all CRUD paths (Filament, API, tinker, etc.).
 */
class LanguageObserver
{
    /**
     * Invalidate supported_locales cache so config is refreshed from DB.
     */
    public function saved(Language $language): void
    {
        Cache::forget(SupportedLocalesService::CACHE_KEY);
    }

    /**
     * Soft delete: cascade soft delete to pages that belong to this language.
     * Only runs when pages table has language_id column.
     */
    public function deleted(Language $language): void
    {
        Cache::forget(SupportedLocalesService::CACHE_KEY);
        if ($language->isForceDeleting()) {
            return;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->each(fn ($page) => $page->delete());
        }
    }

    /**
     * Restore: restore soft-deleted pages that belong to this language.
     */
    public function restored(Language $language): void
    {
        Cache::forget(SupportedLocalesService::CACHE_KEY);
        if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->withTrashed()->each(fn ($page) => $page->restore());
        }
    }
}
