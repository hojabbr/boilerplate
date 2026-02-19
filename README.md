# Laravel React Starter Kit

Laravel 12 application with Inertia.js 2, React 19, Tailwind CSS, and Shadcn UI. Supports localization (mcamara), Fortify auth under locale prefixes, feature flags (Laravel Pennant), light/dark/system theme, and RTL locales.

## Stack

- **Backend:** Laravel 12, Fortify, Filament, Scout (Meilisearch), Reverb, Pennant, mcamara/laravel-localization
- **Frontend:** Inertia 2, React 19, Tailwind CSS v4, Shadcn UI, i18next, Zustand

## Requirements

- PHP 8.2+
- Node 18+
- Composer, npm/pnpm
- (Optional) Docker & Sail for a consistent environment

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure .env: APP_*, DB_*, MEILISEARCH_*, VITE_*, etc.
npm install
npm run build
php artisan migrate
```

With Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
# Configure .env
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
```

## Development

- Backend: `php artisan serve` or `./vendor/bin/sail up`
- Frontend: `npm run dev` (Vite); run with `composer run dev` if using Sail for a single command.
- Admin: Filament at `/admin` (locale-independent).

## Testing

```bash
php artisan test
# Or with Sail:
./vendor/bin/sail artisan test
```

## Architecture and conventions

- **[.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc)** — Backend structure, localization (middleware, locale route group, Fortify under `/{locale}/...`), Scout, Filament, frontend (Shadcn, i18n, theme, RTL), and DevOps/CI.
- **Feature flags:** Laravel Pennant is used for optional features (e.g. registration, 2FA). New features that should be toggled per environment or audience should use Pennant; see ARCHITECTURE “Feature Flags — Laravel Pennant”.
- **Theme:** The UI always uses light / dark / system (e.g. `useAppearance`), applied at the document root; see ARCHITECTURE “Theme (light / dark / system)”.
- **RTL:** RTL locales (e.g. `ar`, `fa`) set `dir="rtl"`; use direction-aware icons and logical spacing (Tailwind start/end, ps/pe) so layouts work in both LTR and RTL; see ARCHITECTURE “RTL and direction‑aware UI”.

## License

MIT.
