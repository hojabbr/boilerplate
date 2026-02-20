<?php

namespace App\Domains\Pages\Observers;

use App\Domains\Pages\Models\Page;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidates page cache for all CRUD operations (create/update/delete/restore/force delete).
 * Ensures changes in Filament or anywhere else are reflected on the next request.
 */
class PageObserver
{
    /**
     * Invalidate cache when attributes that affect the public page change:
     * - is_active: inactive pages must stop being served and disappear from nav/footer.
     * - slug: forget both old and new slug so old URL stops working and new URL is fresh.
     */
    public function updating(Page $page): void
    {
        $slug = $page->getAttribute('slug');
        if ($slug !== null && $page->isDirty('is_active')) {
            Cache::forget(Page::slugCacheKey($slug));
            Cache::forget('menu_pages');
        }
        if ($page->isDirty(['slug', 'show_in_navigation', 'show_in_footer', 'order'])) {
            if ($page->isDirty('slug')) {
                $oldSlug = $page->getOriginal('slug');
                if ($oldSlug !== null) {
                    Cache::forget(Page::slugCacheKey($oldSlug));
                }
                if ($slug !== null) {
                    Cache::forget(Page::slugCacheKey($slug));
                }
            }
            Cache::forget('menu_pages');
        }
    }

    /**
     * Invalidate page cache and active-page list on create or update (Filament or API).
     */
    public function saved(Page $page): void
    {
        $slug = $page->getAttribute('slug');
        if ($slug !== null) {
            Cache::forget(Page::slugCacheKey($slug));
        }
        Cache::forget('menu_pages');
    }

    /**
     * Invalidate page cache and active-page list on soft delete.
     */
    public function deleted(Page $page): void
    {
        $slug = $page->getAttribute('slug');
        if (! $page->isForceDeleting() && $slug !== null) {
            Cache::forget(Page::slugCacheKey($slug));
        }
        Cache::forget('menu_pages');
    }

    /**
     * Invalidate page cache and active-page list on restore (page becomes visible again).
     */
    public function restored(Page $page): void
    {
        $slug = $page->getAttribute('slug');
        if ($slug !== null) {
            Cache::forget(Page::slugCacheKey($slug));
        }
        Cache::forget('menu_pages');
    }

    /**
     * Invalidate page cache and active-page list on force delete (use original slug if model already cleared).
     */
    public function forceDeleted(Page $page): void
    {
        $slug = $page->getAttribute('slug') ?? $page->getOriginal('slug');
        if ($slug !== null) {
            Cache::forget(Page::slugCacheKey($slug));
        }
        Cache::forget('menu_pages');
    }
}
