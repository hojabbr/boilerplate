<?php

namespace App\Core\Observers;

use App\Core\Services\SitemapGenerator;
use Illuminate\Database\Eloquent\Model;

/**
 * Invalidates sitemap cache when observed models are created, updated, deleted, or restored.
 * Attach to any model whose data appears in the sitemap.
 */
class SitemapCacheObserver
{
    public function created(Model $model): void
    {
        SitemapGenerator::clearCache();
    }

    public function updated(Model $model): void
    {
        SitemapGenerator::clearCache();
    }

    public function deleted(Model $model): void
    {
        SitemapGenerator::clearCache();
    }

    public function restored(Model $model): void
    {
        SitemapGenerator::clearCache();
    }

    public function forceDeleted(Model $model): void
    {
        SitemapGenerator::clearCache();
    }
}
