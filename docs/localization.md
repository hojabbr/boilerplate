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
- **config/laravellocalization.php** — `supportedLocales` is overwritten at boot from the DB via **SupportedLocalesService** (cached; only enabled languages). Fallback: when the DB or cache is unavailable (e.g. CI before migrations), injection is skipped and file defaults are used so the app boots. `urlsIgnored`: `/skipped`, `/admin`, `/admin/*`, `/horizon`, `/horizon/*`, `/translations`, `/translations/*` (login/register stay under locale).
- **Content** — Pages and Blog are row-per-locale (`Page` and `BlogPost` have `language_id`; one row per locale per logical page/post). Settings and Landing sections use Spatie Translatable (JSON columns). Language model: `languages` table keyed by code; each language has `is_enabled`.
- **Enabling and disabling languages** — In Filament, open **Languages** (CMS group) and use the **Enabled** toggle per language. Only **enabled** languages are included in `supportedLocales`, the language switcher, and locale-prefixed routes. At least one language must remain enabled; the app default locale is always one of the enabled languages (when you disable the current default, another enabled language is set as default).
- **Locale JSON files** — `lang/*.json` can be managed from the Filament admin panel via **Translation Manager** (Settings group). It uses **spatie/laravel-translation-loader**: translations are stored in the `language_lines` table and optionally synced to/from `lang/*.json`. Database entries override file-based translations when present. The list page shows a **missing keys summary** (locale buttons with counts); each opens **Fill missing translations** (`/admin/language-lines/fill-missing?locale=…`) to fill all missing keys for that locale in one form.

## Frontend

- **i18next** + **i18next-browser-languagedetector**; translation files under `resources/js/i18n/`. Backend passes `locale` (and optionally `locale_switch_urls`) via Inertia shared props; sync i18next and set `document.documentElement.lang` and `dir` (e.g. RTL for `ar`, `fa`).
- Use logical CSS (`ps-*`, `pe-*`) and direction-aware icons for RTL.
