<?php

namespace App\Core\Providers\Filament;

use App\Core\Services\SupportedLocalesService;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;

/**
 * Resolves translatable locales at runtime from the single source of truth
 * (SupportedLocalesService: DB languages table, enabled only, cached) so the
 * Filament locale switcher shows all enabled languages.
 */
class TranslatableLocalesPlugin extends SpatieTranslatablePlugin
{
    /**
     * @return array<int, string>|null
     */
    public function getDefaultLocales(): ?array
    {
        try {
            $supported = app(SupportedLocalesService::class)->get();
        } catch (\Throwable) {
            return null;
        }

        $locales = array_keys($supported);
        if ($locales === []) {
            return null;
        }

        $fallback = config('app.fallback_locale', 'en');
        $key = array_search($fallback, $locales, true);
        if ($key !== false) {
            unset($locales[$key]);
            array_unshift($locales, $fallback);
            $locales = array_values($locales);
        }

        return $locales;
    }
}
