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
- **Content** — Pages are row-per-locale (`Page` has `language_id`). Blog, Settings, Landing sections use Spatie Translatable (JSON columns). Language model: `languages` table keyed by code.

## Frontend

- **i18next** + **i18next-browser-languagedetector**; translation files under `resources/js/i18n/`. Backend passes `locale` (and optionally `locale_switch_urls`) via Inertia shared props; sync i18next and set `document.documentElement.lang` and `dir` (e.g. RTL for `ar`, `fa`).
- Use logical CSS (`ps-*`, `pe-*`) and direction-aware icons for RTL.
