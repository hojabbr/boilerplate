---
title: Admin
layout: default
parent: Features
nav_order: 4
description: 'Filament 5 panel, resources, permissions, translatable models.'
---

# Admin

## Filament 5

The admin panel is at **`/admin`** (locale-independent; in `config/laravellocalization.php` `urlsIgnored`). Resources live under `app/Filament/Resources/` and reference Core or Domain models (e.g. `App\Domains\Blog\Models\BlogPost`). Filament is UI-only; use domain Queries or model scopes, not inline business logic.

## Translation Manager

Locale JSON files (`lang/*.json`) can be managed from the admin panel via **Translation Manager** (Settings group). The implementation uses **spatie/laravel-translation-loader** and a custom Filament resource (kenepa/translation-manager does not support Filament 5). Users need the **manage translations** permission (and the `use-translation-manager` gate). Database translations override file-based ones at runtime.

- **Import from lang files** — Load existing keys from `lang/*.json` into the database. Existing DB values are kept for keys that already exist. The action requires confirmation and shows a success notification with the number of keys and files processed.
- **Export to lang files** — Write all database translations (group `*`) to `lang/*.json`, overwriting those files. Requires confirmation; success notification shows the number of keys and files written.
- **Scan for new keys** — Discovers `__()`, `trans()`, and `@lang()` usage in `app/`, `resources/views/`, and `resources/js/` and adds missing keys to the database (group `*`) without overwriting existing entries. The Translation Manager resource itself is excluded so its UI labels are not added by scan (use **Import from lang files** to load those from `lang/*.json`). Use Scan when you add new translation keys in code and want them in the DB; use **Import** to sync from existing lang files. Artisan: `php artisan translations:scan` (option: `--dry-run`).
- **Locale filters** — Filter the table by **Missing in locale** or **Has translation in locale** (select a locale) to focus on keys that need translation or are already filled.
- **Locales column** — Shows which locales have a non-empty value for each key (badges or comma-separated).
- **Bulk delete** — Select multiple language lines and delete them in one action.
- **Clear all translations** — Removes all translation lines from the database (with confirmation). Use when re-importing from files; cache for the translation loader is cleared.
- **Export CSV** — Downloads all translations (group `*`) as a CSV with columns `key` and one per locale. Useful for external translators or backup.
- **Import CSV** — Upload a CSV with column `key` and one column per locale; keys are created or updated (group `*`). Success notification shows how many rows were imported.

**List missing translations for AI:** Run `php artisan translations:missing` to print all keys that have no translation per locale, with a reference (source) text so you can paste the list to an AI. The command **imports from lang files first** so the list reflects the current state after syncing. Options: `--locale=de` (only one locale), `--format=json` (machine-readable), `--reference=en` (locale to use as source text), `--no-import` (skip import and list missing from current DB only). Add the completed translations in Filament or via CSV import.

## Blog: Generate with AI

The Blog Posts resource includes a **Generate with AI** action that creates posts via the Laravel AI SDK. Generation runs in a queued job (`App\Domains\Blog\Jobs\GenerateBlogPostsJob` on the `blog` queue). You choose languages; the post is generated once in the first language and that same content is translated into each other language (one logical post, multiple language rows). Optional length (short/medium/long) and a shared featured image. When the job finishes (success or failure), the user receives a **Filament database notification** in the admin panel. Ensure Horizon (or your queue worker) is running and `QUEUE_CONNECTION=redis` so the job is processed.

From the same wizard you can create a **scheduled series** (recurring generation): set name, purpose, topics, date range, days of week, hours, optional total post limit, and publish behaviour. The series is stored in `blog_post_series` and processed by the `blog:run-scheduled-series` Artisan command (scheduled every minute in `routes/console.php`). View and deactivate series under **Scheduled series** (`BlogPostSeriesResource`, CMS group); access uses the “manage blog” permission (BlogPostSeriesPolicy).

## Database notifications

The admin panel enables Filament database notifications (`databaseNotifications()`). Users see notifications in the panel (e.g. when a queued job like blog generation completes). Requires the Laravel `notifications` table; on PostgreSQL the `data` column must be `json`/`jsonb`.

## Adding a resource

1. `php artisan make:filament-resource ModelName --generate --soft-deletes` (omit `--soft-deletes` if the model does not use it).
2. Place the resource under `app/Filament/Resources/` and point it at the correct model class.
3. Add a permission (e.g. `manage blog`) in `database/seeders/RoleAndPermissionSeeder.php` and assign it to the admin role.

## Translatable models (Lara Zeus)

For models using **Spatie Laravel Translatable**, use **lara-zeus/spatie-translatable** in Filament: apply the resource/page `Translatable` trait and add `LocaleSwitcher::make()` in `getHeaderActions()` on List, Create, Edit (and View) pages. Configure translatable locales to match `config('laravellocalization.supportedLocales')` or override `getTranslatableLocales()` on the resource.
