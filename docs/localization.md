---
title: Localization
layout: default
parent: Features
nav_order: 1
description: 'Route prefixes, mcamara, RTL, and i18next sync.'
---

# Localization

## Backend

- **mcamara/laravel-localization** prefixes routes with locale (e.g. `/en/dashboard`). `bootstrap/app.php` prepends **LocaleCookieRedirect** to the web stack; the locale route group uses `localeCookieRedirect`, `localizationRedirect`, `localeViewPath`, `setLocalizedFortifyRedirects`.
- **config/laravellocalization.php** — `supportedLocales`; `urlsIgnored`: `/skipped`, `/admin`, `/admin/*` (login/register stay under locale).
- **Content** — Pages and Blog are row-per-locale (`Page` and `BlogPost` have `language_id`; one row per locale per logical page/post). Settings and Landing sections use Spatie Translatable (JSON columns). Language model: `languages` table keyed by code.
- **Locale JSON files** — `lang/*.json` can be managed from the Filament admin panel via **Translation Manager** (Settings group). It uses **spatie/laravel-translation-loader**: translations are stored in the `language_lines` table and optionally synced to/from `lang/*.json`. Database entries override file-based translations when present.

## Frontend

- **i18next** + **i18next-browser-languagedetector**; translation files under `resources/js/i18n/`. Backend passes `locale` (and optionally `locale_switch_urls`) via Inertia shared props; sync i18next and set `document.documentElement.lang` and `dir` (e.g. RTL for `ar`, `fa`).
- Use logical CSS (`ps-*`, `pe-*`) and direction-aware icons for RTL.
