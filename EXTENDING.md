# How to extend this project

**Full step-by-step documentation lives in the [documentation](/docs) folder.** Use these pages as the single source of truth; this file is a short pointer and quick-reference table.

- **[Extending](docs/extending.md)** — Convention overview, where to put code, and links to official Laravel/Inertia/Filament docs.
- **[Scaffolding](docs/scaffolding.md)** — `boilerplate:domain` and `boilerplate:locale` (options, rollback, tests).
- **Backend:** [Backend](docs/backend.md), [Feature flags](docs/feature-flags.md), [Search](docs/search.md), [Admin](docs/admin.md).
- **Frontend:** [Frontend](docs/frontend.md) (pages, layouts, UI, adding a page).
- **Other:** [Localization](docs/localization.md), [Testing](docs/testing.md).

When changing project structure or scaffolding output, update the [boilerplate commands](app/Console/Commands/) and [tests/Feature/BoilerplateCommandsTest.php](tests/Feature/BoilerplateCommandsTest.php) so they stay in sync with the [architecture](docs/architecture.md).

---

## Convention vs documentation (quick reference)

| Area                       | Project custom                                                                                                                                | Full detail                                                                                              |
| -------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Models                     | Core: `app/Core/Models/`. Domain: `app/Domains/<Name>/Models/`. No `app/Models/`. Add to `config/ide-helper.php`, run `ide-helper:models -M`. | [docs/backend.md](docs/backend.md), [Laravel Eloquent](https://laravel.com/docs/12.x/eloquent)           |
| Jobs                       | `app/Domains/<Name>/Jobs/` or `app/Core/Jobs/`. No global `app/Jobs/`.                                                                        | [docs/backend.md](docs/backend.md), [Laravel Queues](https://laravel.com/docs/12.x/queues)               |
| Auth (login, 2FA, profile) | Fortify + Inertia; profile in `app/Domains/Profile/`.                                                                                         | [Laravel Fortify](https://laravel.com/docs/12.x/fortify)                                                 |
| Feature flags              | `app/Core/Features/`, register in AppServiceProvider. Toggleable: `config/features.php` + FeatureFlagSeeder.                                  | [docs/feature-flags.md](docs/feature-flags.md), [Laravel Pennant](https://laravel.com/docs/11.x/pennant) |
| Search                     | Add model to `config/scout.php` `meilisearch.index-settings` (key = full class name).                                                         | [docs/search.md](docs/search.md), [Laravel Scout](https://laravel.com/docs/12.x/scout)                   |
| Filament                   | Resources under `app/Filament/Resources/`; add permission in RoleAndPermissionSeeder.                                                         | [docs/admin.md](docs/admin.md), [Filament 5](https://filamentphp.com/docs/5.x)                           |
| Inertia pages              | `resources/js/features/<name>/pages/`; backend renders component name (e.g. `blog/Index`).                                                    | [docs/frontend.md](docs/frontend.md), [Inertia 2](https://inertiajs.com)                                 |
| Routes/actions (frontend)  | Import from `@/routes` or `@/actions`; run build after adding backend routes.                                                                 | [Laravel Wayfinder](https://github.com/laravel/wayfinder)                                                |

For the complete table and all step-by-step sections, see [docs/extending.md](docs/extending.md).
