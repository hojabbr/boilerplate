<?php

namespace App\Core\Services;

use App\Core\Models\Language;
use Illuminate\Support\Facades\Cache;

/**
 * Single source of truth for supported locales: reads from the languages table (cached).
 * AppServiceProvider injects get() into config at boot so mcamara and all config() callers
 * use DB-driven data. Cache is invalidated by LanguageObserver on language save/delete/restore.
 */
class SupportedLocalesService
{
    public const CACHE_KEY = 'supported_locales';

    public const CACHE_TTL_SECONDS = 3600;

    /**
     * Returns the same shape as config('laravellocalization.supportedLocales').
     * Keys: code => ['name' => string, 'script' => string, 'native' => string, 'regional' => string, 'dir' => 'ltr'|'rtl'].
     *
     * @return array<string, array{name: string, script: string, native: string, regional: string, dir: string}>
     */
    public function get(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return Language::query()
                ->orderBy('sort_order')
                ->get()
                ->keyBy('code')
                ->map(fn (Language $lang): array => [
                    'name' => $lang->name,
                    'script' => $lang->script ?? 'Latn',
                    'native' => $lang->name,
                    'regional' => $lang->regional ?? '',
                    'dir' => in_array($lang->direction, ['ltr', 'rtl'], true) ? $lang->direction : 'ltr',
                ])
                ->all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
