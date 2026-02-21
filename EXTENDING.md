# How to extend this project

This document describes how to add or change features, domains, models, migrations, observers, policies, jobs, feature flags, cache, locales, search, Filament resources, Inertia pages, UI components, themes, and tests. Follow the project structure described here and in [ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc); use official documentation for framework and package APIs.

---

## Convention vs documentation

| Area                          | Follows                      | Project custom                                                                                                                                  | Check docs                                                                                                     |
| ----------------------------- | ---------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| Migrations                    | Laravel                      | Place in `database/migrations/`; naming and schema conventions as in existing migrations                                                        | [Laravel 12 Migrations](https://laravel.com/docs/12.x/migrations)                                              |
| Models                        | Laravel Eloquent             | Core: `app/Core/Models/`. Domain: `app/Domains/<Name>/Models/`. No `app/Models/`. Add to `config/ide-helper.php` and run `ide-helper:models -M` | [Laravel Eloquent](https://laravel.com/docs/12.x/eloquent)                                                     |
| Policies                      | Laravel                      | Core: `app/Core/Policies/`. Domain: `app/Domains/<Name>/Policies/`. Register in `AppServiceProvider::registerPolicies()`                        | [Laravel Authorization](https://laravel.com/docs/12.x/authorization)                                           |
| Queues/Jobs                   | Laravel                      | Jobs in `app/Domains/<Name>/Jobs/` or `app/Core/Jobs/`. No global `app/Jobs/`. Create folder when adding first job                              | [Laravel Queues](https://laravel.com/docs/12.x/queues)                                                         |
| Validation                    | Laravel                      | FormRequests in `app/Domains/<Name>/Http/Requests/`                                                                                             | [Laravel Validation](https://laravel.com/docs/12.x/validation)                                                 |
| Auth (login, 2FA, profile)    | Laravel Fortify              | Fortify views render Inertia; routes under locale prefix. Profile/settings in `app/Domains/Profile/`                                            | [Laravel Fortify](https://laravel.com/docs/11.x/fortify)                                                       |
| Localization (routes, locale) | mcamara/laravel-localization | Routes in locale group; `config/laravellocalization.php` `supportedLocales`                                                                     | [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization)                                |
| Feature flags                 | Laravel Pennant              | Define in `app/Features/`, register in AppServiceProvider. Toggleable: `config/features.php` + FeatureFlagSeeder                                | [Laravel Pennant](https://laravel.com/docs/11.x/pennant)                                                       |
| Search                        | Laravel Scout                | Meilisearch; add model to `config/scout.php` `meilisearch.index-settings` (key = full class name)                                               | [Laravel Scout](https://laravel.com/docs/12.x/scout)                                                           |
| Admin UI                      | Filament                     | Resources under `app/Filament/Resources/`; reference domain/Core models. Add permission in RoleAndPermissionSeeder                              | [Filament 5](https://filamentphp.com/docs/5.x)                                                                 |
| Inertia (pages, forms)        | Inertia.js                   | Pages in `resources/js/features/<name>/pages/`. Component name from backend (e.g. `blog/Index`). Resolver in `app.tsx` / `ssr.tsx`              | [Inertia 2](https://inertiajs.com)                                                                             |
| React / UI                    | React, Shadcn, Radix         | Prefer `components/ui` (Shadcn); then `components/common`; feature-specific in `features/<name>/components/`                                    | [React 19](https://react.dev), [Shadcn UI](https://ui.shadcn.com), [Tailwind v4](https://tailwindcss.com/docs) |
| i18n (frontend)               | i18next                      | Namespaced files under `resources/js/i18n/`; sync with backend locale and shared props                                                          | [i18next](https://www.i18next.com), [react-i18next](https://react.i18next.com)                                 |
| Themes (light/dark/system)    | Project                      | Definitions in `resources/js/themes/`; single source via `useAppearance`; no ad-hoc toggles                                                     | ARCHITECTURE.mdc "Theme"                                                                                       |
| Cache (settings, content)     | Laravel Cache                | Use model cache-key helpers (e.g. `Setting::siteCacheKey()`, `Page::slugCacheKey($slug)`); invalidate in observers                              | This doc, ARCHITECTURE.mdc                                                                                     |
| Routes/actions (frontend)     | Laravel Wayfinder            | Import from `@/routes` or `@/actions`; run build after adding backend routes                                                                    | [Laravel Wayfinder](https://github.com/laravel/wayfinder)                                                      |

---

## Backend: how to add or extend

### New domain

1. Create `app/Domains/<Name>/` with subfolders as needed: `Http/Controllers/`, `Http/Requests/`, `Models/`, `Observers/`, `Policies/`, `Queries/`, `Actions/`, `Services/`, `Jobs/` (create when you add the first file).
2. Register routes in `routes/web.php` or `routes/settings.php` (inside the locale group where applicable).
3. Register the policy in `AppServiceProvider::registerPolicies()`.
4. If the domain has models: add `app/Domains/<Name>/Models` to `config/ide-helper.php` → `model_locations`, then run `php artisan ide-helper:models -M`.

### Migrations

- Use `php artisan make:migration`. Place in `database/migrations/`.
- Follow existing naming (e.g. `create_blog_posts_table`). For translatable models use JSON columns or locale-specific columns as per [Spatie Laravel Translatable](https://github.com/spatie/laravel-translatable).

### Models

- **Core:** Only cross-cutting entities (e.g. Language, Setting, FeatureFlag) in `app/Core/Models/`.
- **Domain:** `app/Domains/<Name>/Models/`. Use `SoftDeletes` for CMS/domain models; document parent/child and cascade in ARCHITECTURE.mdc. Add the model path to `config/ide-helper.php` and run `php artisan ide-helper:models -M`.

### Observers

- **Core:** `app/Core/Observers/` (e.g. SettingObserver).
- **Domain:** `app/Domains/<Name>/Observers/` (e.g. PageObserver).
- Register in `AppServiceProvider::boot()` with `Model::observe(Observer::class)`.
- **Cache invalidation:** If the model is cached (e.g. Setting, Page), use the model’s cache-key helper in the observer (e.g. `Cache::forget(Setting::siteCacheKey())`, `Cache::forget(Page::slugCacheKey($slug))`). For Page, also `Cache::forget('menu_pages')` when nav/footer-affecting attributes change.

### Policies

- **Core:** `app/Core/Policies/`.
- **Domain:** `app/Domains/<Name>/Policies/`.
- Register in `AppServiceProvider::registerPolicies()`. Filament resources use the same policies.

### Queries and Actions

- Domain: `app/Domains/<Name>/Queries/`, `app/Domains/<Name>/Actions/`. Controllers call these; keep controllers thin.

### Jobs

- Create `app/Domains/<Name>/Jobs/` or `app/Core/Jobs/` when adding the first job. Implement `ShouldQueue`; follow [Laravel queue conventions](https://laravel.com/docs/12.x/queues). There is no global `app/Jobs/`.

### Feature flags (Pennant)

1. Create a feature class in `app/Features/` (e.g. `MyFeature.php`) implementing Pennant’s feature interface.
2. Register in `AppServiceProvider::boot()` with `Feature::define('my-feature', MyFeature::class)`.
3. If admin-toggleable: add key and label to `config/features.php` under `toggleable`, then run `php artisan db:seed --class=FeatureFlagSeeder`.
4. Gate routes/controllers and Filament with `Feature::active('my-feature')` (and `abort(404)` or equivalent when inactive).

### Settings and site cache

- Singleton: `Setting::site()` uses `Setting::siteCacheKey()`. TTL from `config('cache.content_ttl')`.
- Invalidate in `SettingObserver` with `Cache::forget(Setting::siteCacheKey())`. Do not hardcode `'setting.site'` so that namespace moves do not break cache.

### Content cache (e.g. page by slug)

- Use a static cache-key method on the model (e.g. `Page::slugCacheKey($slug)`). Use it in the query class (e.g. `GetPageBySlug`) for `Cache::remember()` and in the observer for `Cache::forget()`. Same pattern for any new cached entity.

### Landing page / sections

- Sections are loaded in `LandingService::getSectionsForLocale()` and cached by `landing_sections.{locale}`. Invalidate in `LandingSectionObserver` and `LandingSectionItemObserver` with `Cache::forget("landing_sections.{$locale}")`. New section types: add to the LandingSection type/config and to the frontend welcome page.

### Locales / languages

- **Backend:** Edit `config/laravellocalization.php` → `supportedLocales` (add or uncomment). If the app uses the `languages` table, seed it from this config (e.g. Language seeder).
- **Frontend:** Keep i18next and backend in sync; `locale_switch_urls` from backend drive the language switcher.

### Search (Scout / Meilisearch)

1. Add the searchable model to `config/scout.php` under `meilisearch.index-settings` with key = full model class (e.g. `App\Domains\Blog\Models\BlogPost`).
2. Implement `toSearchableArray()` on the model. Use the `Searchable` trait.
3. Run `php artisan scout:import "App\Domains\Blog\Models\BlogPost"` (or flush/sync) after schema or searchable-data changes.

### Filament resource

1. Run `php artisan make:filament-resource ModelName --generate --soft-deletes` if the model uses soft deletes (omit `--soft-deletes` otherwise).
2. Ensure the resource lives under `app/Filament/Resources/` and references the domain or Core model class (e.g. `App\Domains\Blog\Models\BlogPost`).
3. Add a permission (e.g. `manage blog`) in `database/seeders/RoleAndPermissionSeeder.php` and assign it to the admin role.
4. For translatable models use [Lara Zeus Spatie Translatable](https://github.com/lara-zeus/spatie-translatable) and `LocaleSwitcher::make()` in header actions.

### Helpers

- Global: `app/Core/Support/` (or a dedicated `app/Helpers` if you introduce one). Domain-specific: inside the domain. Create the directory when adding the first file; no `.gitkeep`.

### Soft deletes and cascading

- Use `SoftDeletes` on domain/CMS models. Document parent/child and force-delete behavior in ARCHITECTURE.mdc. Cascaded soft deletes via observers or model events; cascaded force deletes via DB foreign keys and observers.

---

## Frontend: how to add or extend

### New feature module

1. Create `resources/js/features/<name>/` with `pages/`, `components/`, `hooks/`, `services/`, `types.ts`, `index.ts` as needed.
2. Backend component name (e.g. `blog/Index`) resolves to `features/blog/pages/Index.tsx` via `pagePath()` in `app.tsx` and `ssr.tsx` (merged glob). No `.gitkeep`.

### New Inertia page

1. Add a `.tsx` file under `resources/js/features/<name>/pages/` (e.g. `Show.tsx`).
2. Backend must call `Inertia::render('<name>/Show', ...)` (e.g. `blog/Show`). Single-segment name `welcome` is mapped to `features/landing/pages/welcome.tsx` in the resolver.
3. PHP tests use `App\Core\Inertia\TestingViewFinder`; no change needed if naming follows this convention.

### New UI component

- Prefer Shadcn components in `components/ui` first. Then `components/common` for shared compositions. Feature-specific: `features/<name>/components/`.

### Themes

- Add a new theme file in `resources/js/themes/` (e.g. `newTheme.ts`), export it from `themes/index.ts`, and extend the `Theme` type. Use `useAppearance`; do not add ad-hoc theme toggles.

### i18n

- Add namespaced translation files under `resources/js/i18n/`. Use with i18next. Keep in sync with backend locale; use shared `translations` or keys from backend when applicable.

### Routes / forms (Wayfinder)

- Import from `@/routes` or `@/actions`. After adding backend routes, run `npm run build` (or dev) so Wayfinder regenerates. Use the typed route/action in React for links and forms.

---

## Tests

### Backend

- Pest in `tests/Feature/` or `tests/Unit/`. Feature tests: `assertInertia()->component('blog/Index')` etc.; the component path is resolved by `App\Core\Inertia\TestingViewFinder`. For feature-flagged routes, activate the flag in the test (e.g. `Feature::activate('blog')`). For cache assertions use the model’s cache-key helpers (e.g. `Setting::siteCacheKey()`, `Page::slugCacheKey('slug')`) as in `tests/Feature/ContentCacheInvalidationTest.php`.

### Frontend

- Jest + React Testing Library in `resources/js/__tests__/` (or project convention). E2E: Playwright via the Pest browser plugin.

---

## Edge cases and gotchas

- **Cache key after moving model namespace:** Always use a key derived from the class (e.g. `Setting::siteCacheKey()`, `Page::slugCacheKey($slug)`) so old serialized cache entries are not read after a move. Run `php artisan cache:clear` once after deploy if you previously cached under a fixed string.
- **Feature-flagged public routes:** Controllers should `abort(404)` (or equivalent) when the feature is inactive; tests should activate the feature when testing that route.
- **RTL:** Use logical CSS (e.g. `ps-*`, `pe-*`), direction-aware icons, and set `dir` from locale. RTL locales are documented in ARCHITECTURE.mdc.
- **Filament and domain models:** Filament resources live under `app/Filament/` but reference `App\Domains\*` or `App\Core\*` models. Do not put business logic in Filament; use domain Queries/Actions.
