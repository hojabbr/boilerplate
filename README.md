# Laravel React Boilerplate

[![CI](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml/badge.svg)](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

A production-ready starter for **Laravel 12**, **Inertia 2**, and **React 19** with localization, Filament admin, feature flags, full-text search, and real-time support. Use it as a template or clone to build full-stack apps without redoing auth, i18n, theme, or tooling.

**Repository:** [github.com/hojabbr/boilerplate](https://github.com/hojabbr/boilerplate)

---

## What’s included

- **Full-stack app** — Laravel backend + Inertia SSR + React frontend, no separate API layer
- **Auth** — Login, registration, password reset, email verification, optional 2FA (Laravel Fortify)
- **Admin** — Filament 5 panel at `/admin` with users, roles, settings, blog, pages, contact submissions, feature flags
- **Localization** — Route prefixes per locale (mcamara), translatable content (Spatie), RTL (e.g. Arabic, Farsi)
- **Theme** — Light / dark / system with persistent preference (cookie + localStorage)
- **Search** — Laravel Scout + Meilisearch for typo-tolerant, faceted search
- **Real-time** — Laravel Reverb (WebSockets) + Laravel Echo for broadcasts
- **Feature flags** — Laravel Pennant to toggle blog, static pages, contact form, registration, 2FA
- **Code quality** — Pint, PHPStan, ESLint, Prettier, Husky + Commitlint, optional semantic-release

---

## Tech stack

### Backend (PHP)

| Area              | Packages                                                                |
| ----------------- | ----------------------------------------------------------------------- |
| **Framework**     | Laravel 12                                                              |
| **Auth**          | Laravel Fortify (credentials, 2FA, profile)                             |
| **Admin**         | Filament 5, Lara Zeus (Spatie Translatable in Filament)                 |
| **Localization**  | mcamara/laravel-localization (route prefixes, locale detection)         |
| **Content**       | Spatie Laravel Translatable, Spatie Media Library, Spatie Query Builder |
| **Permissions**   | Spatie Laravel Permission (roles & permissions)                         |
| **Search**        | Laravel Scout, Meilisearch (meilisearch-php)                            |
| **Real-time**     | Laravel Reverb                                                          |
| **Feature flags** | Laravel Pennant                                                         |
| **Queue / dev**   | Laravel Horizon, Laravel Telescope, Laravel Pail                        |
| **Routes → TS**   | Laravel Wayfinder (typed route helpers for frontend)                    |
| **OAuth**         | Laravel Socialite                                                       |
| **Media**         | pbmedia/laravel-ffmpeg (video/audio processing)                         |

### Frontend (JavaScript / TypeScript)

| Area          | Packages                                                               |
| ------------- | ---------------------------------------------------------------------- |
| **Stack**     | React 19, Inertia 2, Vite 7, TypeScript                                |
| **Styling**   | Tailwind CSS v4, Tailwind Merge, class-variance-authority              |
| **UI**        | Shadcn-style components (Radix UI, Headless UI, Base UI), Lucide icons |
| **i18n**      | i18next, react-i18next, i18next-browser-languagedetector               |
| **State**     | Zustand                                                                |
| **Forms**     | React Hook Form, Zod, @hookform/resolvers                              |
| **Motion**    | Motion (LazyMotion + domAnimation)                                     |
| **Real-time** | Laravel Echo, Pusher JS                                                |

### DevOps & quality

- **CI:** GitHub Actions (lint, PHPStan, Pint, ESLint, Prettier, Pest, Playwright)
- **PHP:** Laravel Pint, Larastan (PHPStan), Pest 4, Pest Browser (Playwright)
- **JS/TS:** ESLint, Prettier (with Tailwind plugin), Husky, Commitlint (Conventional Commits)
- **Releases:** Optional semantic-release (changelog, tags, GitHub releases)

### Project structure

- **Backend:** `app/Core/` (contracts, exceptions, middleware, models like Language/Setting/FeatureFlag, observers, policies, providers, shared services like PagePropsService), `app/Domains/` (Auth, Blog, Contact, Page, Dashboard, Profile, Landing, Search — each with Http/Controllers, Http/Requests, Models, Actions, DTOs, Queries, Services, Observers, Policies). Controllers live in each domain and are thin. Filament is UI-only and uses domain or Core models. Models live in `app/Core/Models/` or `app/Domains/<Name>/Models/`, not `app/Models/`. Jobs live in `Domains/<Name>/Jobs/` or `Core/Jobs/` (create the folder when adding the first job). Pennant feature-flag definitions live in `app/Features/`.
- **Frontend:** `resources/js/` — Inertia pages live in `resources/js/features/<name>/pages/` (see ARCHITECTURE and EXTENDING.md). Feature modules (auth, blog, contact, dashboard, landing, pages, profile, settings) each have `pages/`, `components/`, `hooks/`, `services/`, `types.ts`, `index.ts` as needed; shared UI in `components/` (ui, common), plus `layouts/`, `hooks/`, `store/`, `themes/`, `services/`, `types/`. Use `@/` for `resources/js` and optionally `@features/*` for feature modules.

---

## Optional features (gated by Pennant)

These can be turned on or off via **Filament → Settings → Feature flags** (or in code):

- **Blog** — Translatable posts, WYSIWYG, media (gallery, videos, documents), public listing and show with lightbox
- **Static pages** — Per-locale CMS pages (e.g. Privacy, Terms)
- **Contact form** — Public form with Filament list/edit of submissions
- **Registration** — Fortify registration
- **Two-factor authentication** — TOTP-based 2FA for users

---

## Requirements

- **PHP** 8.5+
- **Node** 24+
- **Composer** and **npm** (or pnpm)
- Optional: **Docker** and [Laravel Sail](https://laravel.com/docs/sail) for a consistent environment

---

## Installation

1. **Clone** (or use [Use this template](https://github.com/hojabbr/boilerplate/generate)):

    ```bash
    git clone https://github.com/hojabbr/boilerplate.git
    cd boilerplate
    ```

2. **Backend setup:**

    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate
    ```

    Edit `.env`: set `APP_NAME`, `APP_URL`, `DB_*`. Optionally set `MEILISEARCH_*`, `REVERB_*`, etc. (see [.env.example](.env.example)).

3. **Frontend setup:**

    ```bash
    npm install
    npm run build
    ```

4. **Database:**

    ```bash
    php artisan migrate
    ```

    Optional: `php artisan db:seed`.

### With Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
cp .env.example .env
./vendor/bin/sail artisan key:generate
# Edit .env as needed
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
```

---

## Development

- **Backend:** `php artisan serve` or `./vendor/bin/sail up`
- **Frontend (Vite):** `npm run dev`
- **All-in-one (server, queue, logs, Vite):** `composer run dev` (with Sail, run inside the container)
- **Admin:** [http://localhost/admin](http://localhost/admin) (locale-independent)
- **Scaffolding:** `php artisan boilerplate:domain` and `php artisan boilerplate:locale` scaffold new domains and locales (use `--dry-run` to preview, `--rollback=<name>` to undo). Scaffolding output is kept in sync with ARCHITECTURE; structure changes that affect it require command and test updates—see [EXTENDING.md](EXTENDING.md) and [.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc).

---

## Testing

```bash
php artisan test
```

With Sail:

```bash
./vendor/bin/sail artisan test
```

Run tests before submitting changes; see [CONTRIBUTING.md](CONTRIBUTING.md).

---

## Documentation

- **[.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc)** — Backend and frontend architecture, localization, feature flags, theme, RTL, DevOps, and conventions. Useful for contributors and for AI-assisted development (e.g. Cursor).
- **[EXTENDING.md](EXTENDING.md)** — How to add or extend features, domains, models, migrations, observers, policies, jobs, feature flags, cache, locales, search, Filament resources, Inertia pages, UI components, themes, and tests; Laravel vs project conventions and when to check official docs (Laravel, Inertia, Filament, mcamara, Pennant, Scout, Wayfinder, React, Shadcn).
- **Naming:** Backend uses PascalCase for PHP files and classes; frontend follows ARCHITECTURE (see “Directory Structure” and “Naming conventions” there): kebab-case for page/component/layout files, PascalCase for React component names and hooks.
- **Feature flags** — Laravel Pennant; see ARCHITECTURE “Feature Flags — Laravel Pennant”.
- **Theme** — Single source of truth (`useAppearance`) for light/dark/system; see ARCHITECTURE “Theme (light / dark / system)”.
- **RTL** — RTL locales set `dir="rtl"`; use direction-aware icons and logical spacing; see ARCHITECTURE “RTL and direction‑aware UI”.

---

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and our [Code of Conduct](CODE_OF_CONDUCT.md).

---

## License

This project is open-sourced under the [MIT License](LICENSE).
