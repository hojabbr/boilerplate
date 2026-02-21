# Laravel React Boilerplate

[![CI](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml/badge.svg)](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

A production-ready starter for **Laravel 12**, **Inertia 2**, and **React 19** with localization, Filament admin, feature flags, full-text search, and real-time support. Use it as a template or clone to build full-stack apps without redoing auth, i18n, theme, or tooling.

**DOCUMENTATION:** [hojabbr.github.io/boilerplate/](https://hojabbr.github.io/boilerplate/)

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

- **Backend:** `app/Core/` (cross-cutting) and `app/Domains/<Name>/` (vertical slices). No `app/Models/`; models in Core or Domains. Jobs in `Domains/<Name>/Jobs/` or `Core/Jobs/`. See [docs: Architecture → Backend](docs/backend.md).
- **Frontend:** Inertia pages in `resources/js/features/<name>/pages/`; shared UI in `components/` (ui, common). See [docs: Architecture → Frontend](docs/frontend.md).

---

## Optional features

**Filament → Settings → Feature flags** toggles: Blog, Static pages, Contact form. Registration and two-factor authentication are controlled via Laravel Fortify configuration, not the Filament feature-flags list.

- **Blog** — Translatable posts, WYSIWYG, media (gallery, videos, documents), public listing and show with lightbox
- **Static pages** — Per-locale CMS pages (e.g. Privacy, Terms)
- **Contact form** — Public form with Filament list/edit of submissions
- **Registration** — Fortify registration (enable/disable in Fortify config)
- **Two-factor authentication** — TOTP-based 2FA (Fortify)

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
- **Scaffolding:** `php artisan boilerplate:domain` and `php artisan boilerplate:locale` (use `--dry-run`, `--rollback=<name>`). See [docs: Scaffolding](docs/scaffolding.md).

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

**Canonical documentation** is in the [`/docs`](docs/) folder and on [GitHub Pages](https://hojabbr.github.io/boilerplate/) (when enabled). It covers installation, configuration, architecture (overview, backend, frontend), features (localization, feature flags, search, admin), development (extending, scaffolding, testing), DevOps, and reference (paths, config, env). Use it as the single source of truth.

- **[docs/](docs/)** — Start at [docs/index.md](docs/index.md) or the GitHub Pages site for the full guide and sidebar navigation.
- **[EXTENDING.md](EXTENDING.md)** — Short summary and links to the extending and scaffolding docs.
- **[.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc)** — Condensed rules for Cursor/IDE; points to /docs for full architecture and conventions.

---

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and our [Code of Conduct](CODE_OF_CONDUCT.md).

---

## License

This project is open-sourced under the [MIT License](LICENSE).
