<?php

namespace App\Core\Observers;

use App\Core\Models\Language;
use App\Core\Services\SupportedLocalesService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Cascades soft delete/restore to related pages when a language is soft-deleted or restored.
 * BlogPosts are cascade-deleted by DB FK when a language is force-deleted.
 * Invalidates supported_locales cache so next request gets DB-driven config.
 * Enforces: cannot disable the last enabled language; when disabling the default, another enabled becomes default.
 * Fires for all CRUD paths (Filament, API, tinker, etc.).
 */
class LanguageObserver
{
    /**
     * Prevent disabling the last enabled language.
     */
    public function updating(Language $language): void
    {
        if ($language->is_enabled === false && $language->getOriginal('is_enabled') === true) {
            $otherEnabledExists = Language::query()
                ->where('id', '!=', $language->id)
                ->where('is_enabled', true)
                ->exists();
            if (! $otherEnabledExists) {
                throw ValidationException::withMessages([
                    'is_enabled' => ['At least one language must remain enabled. Enable another language first.'],
                ]);
            }
        }
    }

    /**
     * Invalidate supported_locales cache so config is refreshed from DB.
     * When disabling the default language, assign default to another enabled language.
     */
    public function saved(Language $language): void
    {
        if ($language->is_enabled === false && $language->is_default === true) {
            $newDefault = Language::query()
                ->where('id', '!=', $language->id)
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->first();
            if ($newDefault !== null) {
                $newDefault->update(['is_default' => true]);
            }
            $language->updateQuietly(['is_default' => false]);
        }

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
        if (Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->each(fn ($page) => $page->delete());
        }
    }

    /**
     * Restore: restore soft-deleted pages that belong to this language.
     */
    public function restored(Language $language): void
    {
        Cache::forget(SupportedLocalesService::CACHE_KEY);
        if (Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->withTrashed()->each(fn ($page) => $page->restore());
        }
    }
}
