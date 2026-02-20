# Laravel React Boilerplate

[![CI](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml/badge.svg)](https://github.com/hojabbr/boilerplate/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

A starter kit for building localized Laravel 12 + Inertia 2 + React 19 applications. Use this as a template or clone to kickstart full-stack apps with auth, admin, feature flags, search, and real-time support.

**Repository:** [github.com/hojabbr/boilerplate](https://github.com/hojabbr/boilerplate)

## Features

- **Backend:** Laravel 12, Fortify (auth), Filament (admin), Scout + Meilisearch, Reverb (WebSockets), Pennant (feature flags), mcamara/laravel-localization
- **Frontend:** Inertia 2, React 19, Tailwind CSS v4, Shadcn UI, i18next, Zustand
- **Localization:** Route prefixes per locale, translatable content (Spatie), RTL support (e.g. Arabic, Farsi)
- **Theme:** Light / dark / system with persistent preference
- **Optional features:** Blog, static pages, contact form, 2FA, registration — toggled via feature flags

## Requirements

- PHP 8.5+
- Node 24+
- Composer, npm (or pnpm)
- Optional: Docker & [Laravel Sail](https://laravel.com/docs/sail) for a consistent environment

## Installation

1. **Clone the repository** (or use [Use this template](https://github.com/hojabbr/boilerplate/generate)):

    ```bash
    git clone https://github.com/hojabbr/boilerplate.git
    cd boilerplate
    ```

2. **Install dependencies and configure environment:**

    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate
    ```

    Edit `.env` and set at least: `APP_NAME`, `APP_URL`, `DB_*`, and optionally `MEILISEARCH_*`, `REVERB_*`, etc. See [.env.example](.env.example) for all options.

3. **Install frontend dependencies and build:**

    ```bash
    npm install
    npm run build
    ```

4. **Run migrations:**

    ```bash
    php artisan migrate
    ```

    Optionally seed: `php artisan db:seed`.

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

## Development

- **Backend:** `php artisan serve` or `./vendor/bin/sail up`
- **Frontend (Vite):** `npm run dev` — or use `composer run dev` for server, queue, logs, and Vite in one command (with Sail: run inside the container).
- **Admin panel:** Filament at `/admin` (locale-independent).

## Testing

```bash
php artisan test
```

With Sail:

```bash
./vendor/bin/sail artisan test
```

Contributors should run tests before submitting changes; see [CONTRIBUTING.md](CONTRIBUTING.md).

## Documentation

- **[.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc)** — Backend and frontend architecture, localization, feature flags, theme, RTL, DevOps, and conventions. Useful for contributors and for AI-assisted development (e.g. Cursor).
- **Feature flags:** Laravel Pennant gates optional features (registration, 2FA, blog, pages, contact). See ARCHITECTURE “Feature Flags — Laravel Pennant”.
- **Theme:** Single source of truth (`useAppearance`) for light/dark/system; see ARCHITECTURE “Theme (light / dark / system)”.
- **RTL:** RTL locales set `dir="rtl"`; use direction-aware icons and logical spacing; see ARCHITECTURE “RTL and direction‑aware UI”.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and our [Code of Conduct](CODE_OF_CONDUCT.md).

## License

This project is open-sourced under the [MIT License](LICENSE).
