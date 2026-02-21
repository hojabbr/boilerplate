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

## Adding a resource

1. `php artisan make:filament-resource ModelName --generate --soft-deletes` (omit `--soft-deletes` if the model does not use it).
2. Place the resource under `app/Filament/Resources/` and point it at the correct model class.
3. Add a permission (e.g. `manage blog`) in `database/seeders/RoleAndPermissionSeeder.php` and assign it to the admin role.

## Translatable models (Lara Zeus)

For models using **Spatie Laravel Translatable**, use **lara-zeus/spatie-translatable** in Filament: apply the resource/page `Translatable` trait and add `LocaleSwitcher::make()` in `getHeaderActions()` on List, Create, Edit (and View) pages. Configure translatable locales to match `config('laravellocalization.supportedLocales')` or override `getTranslatableLocales()` on the resource.
