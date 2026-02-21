---
title: Scaffolding
layout: default
parent: Development
nav_order: 2
description: 'boilerplate:domain and boilerplate:locale; options, rollback, and tests.'
---

# Scaffolding

Two Artisan commands keep backend, Filament, and frontend in sync. They use [Laravel Prompts](https://laravel.com/docs/12.x/prompts) for input.

## boilerplate:locale

Adds a new locale end-to-end.

**Prompts:** Locale code (e.g. `pt`, `hi`), metadata (name/script/native/regional), RTL yes/no, and whether to add placeholder entries to SettingSeeder, PageSeeder, and LandingSectionSeeder.

**Actions:** Updates `config/laravellocalization.php`, `resources/js/i18n/index.ts`, optionally `app.tsx` (RTL), creates `lang/{code}.json` from `lang/en.json`, and optionally updates the three seeders.

**Options:**

- `--dry-run` — Show planned changes without applying.
- `--rollback=<code>` — Remove the locale from config, i18n, lang file, seeders, and RTL list.

**After running:** `php artisan wayfinder:generate`; if seeder updates were chosen, `php artisan db:seed`. Translate `lang/{code}.json` and seeder content.

## boilerplate:domain

Scaffolds a new domain (vertical slice).

**Prompts:** Domain name (PascalCase, **singular**), route path (kebab-case), create model (table name, migration, Translatable, Scout), Filament resource, Pennant feature flag, frontend feature module.

**Actions:** Creates `app/Domains/{Name}/Http/Controllers/`, registers route in the locale group, and optionally model (with migration), Scout config entry, Filament resource and permission, feature class and config, and `resources/js/features/{path}/` with a placeholder Index page.

**Options:**

- `--dry-run` — List planned actions without creating or editing files.
- `--rollback=<Name>` — Remove the scaffolded domain (controller, route, model, migration, Filament resource, feature class, config edits, frontend module).

**After running:** `php artisan wayfinder:generate`, run migrations if a new migration was added, and for searchable models `php artisan scout:import "App\Domains\...\Model"`. If a feature flag was added, add it to `config/features.php` if toggleable and run `php artisan db:seed --class=FeatureFlagSeeder`.

## Keeping scaffolding in sync

When you change project structure or conventions that affect command output, update:

- `app/Core/Console/Commands/BoilerplateDomainCommand.php`
- `app/Core/Console/Commands/BoilerplateLocaleCommand.php`
- `tests/Feature/BoilerplateCommandsTest.php`

Run the boilerplate tests so they still pass; they define the contract for non-interactive behavior, rollback, and dry-run.
