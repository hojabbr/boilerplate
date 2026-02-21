---
title: Paths, config & env
layout: default
parent: Reference
nav_order: 1
description: 'Package versions, paths, config files, and environment variables.'
---

# Paths, config & env

## Package versions (representative)

| Area      | Package                   | Version |
| --------- | ------------------------- | ------- |
| PHP       | php                       | 8.5+    |
| Framework | laravel/framework         | v12     |
| Auth      | laravel/fortify           | v1      |
| Admin     | filament/filament         | v5      |
| Inertia   | inertiajs/inertia-laravel | v2      |
| Scout     | laravel/scout             | v10     |
| Pennant   | laravel/pennant           | v1      |
| Reverb    | laravel/reverb            | v1      |
| Wayfinder | laravel/wayfinder         | v0      |
| Frontend  | @inertiajs/react          | v2      |
| Frontend  | react                     | v19     |
| Frontend  | tailwindcss               | v4      |
| Testing   | pestphp/pest              | v4      |
| Quality   | laravel/pint              | v1      |
| Quality   | larastan/larastan         | v3      |

Check `composer.json`, `package.json`, and `AGENTS.md` in the repository for the exact versions.

## Key paths

| Purpose                    | Path                                                                                                             |
| -------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| Core models                | `app/Core/Models/`                                                                                               |
| Core middleware            | `app/Core/Middleware/`                                                                                           |
| Core services              | `app/Core/Services/`                                                                                             |
| Core Inertia (tests)       | `app/Core/Inertia/TestingViewFinder.php`                                                                         |
| Domain slice               | `app/Domains/<Name>/` (e.g. Blog, Page)                                                                          |
| Domain controller          | `app/Domains/<Name>/Http/Controllers/`                                                                           |
| Domain model               | `app/Domains/<Name>/Models/`                                                                                     |
| Feature flags (Pennant)    | `app/Core/Features/`                                                                                             |
| Filament resources         | `app/Filament/Resources/`                                                                                        |
| Inertia pages              | `resources/js/features/<name>/pages/`                                                                            |
| Shared UI                  | `resources/js/components/ui/`, `resources/js/components/common/`                                                 |
| Routes                     | `routes/web.php`, `routes/settings.php`                                                                          |
| IDE helper model locations | `config/ide-helper.php` → `model_locations`                                                                      |
| Scout index settings       | `config/scout.php` → `meilisearch.index-settings` (key = full model class)                                       |
| Toggleable feature flags   | `config/features.php` → `toggleable`                                                                             |
| Localization config        | `config/laravellocalization.php`                                                                                 |
| AI (blog generation)       | `config/ai.php` → `blog.failover_providers`, `provider_capabilities`; uses Laravel AI SDK provider default model |

## Environment variables (main)

| Variable                                              | Purpose                      |
| ----------------------------------------------------- | ---------------------------- |
| `APP_NAME`, `APP_URL`, `APP_KEY`                      | Application identity and key |
| `DB_CONNECTION`, `DB_*`                               | Database                     |
| `SCOUT_DRIVER`, `MEILISEARCH_HOST`, `MEILISEARCH_KEY` | Search (Scout + Meilisearch) |
| `REVERB_*`, `VITE_REVERB_*`                           | WebSockets (Reverb)          |
| `VITE_APP_NAME`                                       | Frontend app name            |

See `.env.example` in the repository for the full list.

## Commands

| Command                                                       | Purpose                                                               |
| ------------------------------------------------------------- | --------------------------------------------------------------------- |
| `php artisan wayfinder:generate`                              | Regenerate route/action TypeScript after route changes                |
| `php artisan ide-helper:models -M`                            | Regenerate IDE helper for models (after adding model paths to config) |
| `php artisan scout:import "App\Domains\Blog\Models\BlogPost"` | Import/searchable model into Meilisearch                              |
| `php artisan boilerplate:domain`                              | Scaffold a new domain                                                 |
| `php artisan boilerplate:locale`                              | Add a new locale                                                      |
| `vendor/bin/pint --format agent`                              | Fix PHP style                                                         |
| `vendor/bin/phpstan analyse`                                  | PHP static analysis                                                   |
| `npm run build`                                               | Build frontend (and Wayfinder)                                        |
| `npm run dev`                                                 | Vite dev server                                                       |
| `php artisan test`                                            | Run tests                                                             |
