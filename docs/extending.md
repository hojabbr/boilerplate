---
title: Extending
layout: default
parent: Development
nav_order: 1
description: 'How to add domains, models, pages, feature flags, Filament resources, and tests.'
---

# Extending

This page summarizes where to put new code and which conventions to follow. The repository’s [EXTENDING.md](https://github.com/hojabbr/boilerplate/blob/main/EXTENDING.md) has step-by-step instructions and the full convention table.

## Convention overview

| Area                       | Project custom                                                                                                                                | Check docs                                                           |
| -------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------- |
| Models                     | Core: `app/Core/Models/`. Domain: `app/Domains/<Name>/Models/`. No `app/Models/`. Add to `config/ide-helper.php`, run `ide-helper:models -M`. | [Laravel Eloquent](https://laravel.com/docs/12.x/eloquent)           |
| Policies                   | Core: `app/Core/Policies/`. Domain: `app/Domains/<Name>/Policies/`. Register in `AppServiceProvider::registerPolicies()`.                     | [Laravel Authorization](https://laravel.com/docs/12.x/authorization) |
| Jobs                       | `app/Domains/<Name>/Jobs/` or `app/Core/Jobs/`. No global `app/Jobs/`. Create folder when adding first job.                                   | [Laravel Queues](https://laravel.com/docs/12.x/queues)               |
| Auth (login, 2FA, profile) | Fortify views render Inertia; routes under locale prefix. Profile in `app/Domains/Profile/`.                                                  | [Laravel Fortify](https://laravel.com/docs/12.x/fortify)             |
| Feature flags              | Define in `app/Features/`, register in AppServiceProvider. Toggleable: `config/features.php` + FeatureFlagSeeder.                             | [Laravel Pennant](https://laravel.com/docs/11.x/pennant)             |
| Search                     | Add model to `config/scout.php` `meilisearch.index-settings` (key = full class name).                                                         | [Laravel Scout](https://laravel.com/docs/12.x/scout)                 |
| Filament                   | Resources under `app/Filament/Resources/`; reference domain/Core models. Add permission in RoleAndPermissionSeeder.                           | [Filament 5](https://filamentphp.com/docs/5.x)                       |
| Inertia pages              | `resources/js/features/<name>/pages/`. Backend renders component name (e.g. `blog/Index`).                                                    | [Inertia 2](https://inertiajs.com)                                   |
| Routes/actions (frontend)  | Import from `@/routes` or `@/actions`; run build after adding backend routes.                                                                 | [Laravel Wayfinder](https://github.com/laravel/wayfinder)            |

## Backend: quick steps

- **New domain:** Create `app/Domains/<Name>/` with Http, Models, etc.; register routes and policy. Add model path to `config/ide-helper.php` if needed. Or use [Scaffolding](scaffolding.md).
- **Migrations:** `php artisan make:migration`; place in `database/migrations/`.
- **Observers:** `app/Core/Observers/` or `app/Domains/<Name>/Observers/`; register in `AppServiceProvider::boot()`. Use model cache-key helpers for cache invalidation (e.g. `Setting::siteCacheKey()`, `Page::slugCacheKey($slug)`).
- **Feature flag (toggleable):** Feature class in `app/Features/`, register with `Feature::define()`, add to `config/features.php`, run FeatureFlagSeeder, gate with `Feature::active()`.

## Frontend: quick steps

- **New feature module:** Create `resources/js/features/<name>/` with `pages/`, `components/`, etc. Backend component name (e.g. `blog/Index`) resolves via `pagePath()` to `features/blog/pages/Index.tsx`.
- **New Inertia page:** Add `.tsx` under `features/<name>/pages/`; backend calls `Inertia::render('<name>/PageName', ...)`. Single-segment `welcome` → `features/landing/pages/welcome.tsx`.
- **UI:** Prefer `components/ui` (Shadcn), then `components/common`, then feature-specific components.
- **Wayfinder:** After adding backend routes, run `npm run build` (or dev).

## Edge cases

- Use model-based cache keys (e.g. `Setting::siteCacheKey()`) so cache survives namespace moves; run `php artisan cache:clear` after deploy if needed.
- Feature-flagged routes: controllers `abort(404)` when inactive; tests use `Feature::activate('key')` when testing that route.
- Filament resources reference `App\Domains\*` or `App\Core\*` models; keep business logic in domain Queries/Actions.

For full detail and the complete table, see [EXTENDING.md](https://github.com/hojabbr/boilerplate/blob/main/EXTENDING.md) in the repository.
