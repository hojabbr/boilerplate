# Contributing to Laravel React Boilerplate

Thank you for your interest in contributing. This document explains how to get set up and submit changes.

## Development setup

1. **Clone and install**

    ```bash
    git clone https://github.com/hojabbr/boilerplate.git
    cd boilerplate
    composer install
    cp .env.example .env
    php artisan key:generate
    npm install
    npm run build
    php artisan migrate
    ```

    You can use [Laravel Sail](https://laravel.com/docs/sail) for a full Docker-based stack: `./vendor/bin/sail up -d` and run the above commands via `./vendor/bin/sail ...`.

2. **Configure `.env`**  
   Set at least `APP_NAME`, `APP_URL`, and database (`DB_*`). For tests, the project’s CI uses SQLite in-memory; locally you can use any supported driver.

## Conventions and where to put new code

- **New backend domain** — `app/Domains/<Name>/` (Http, Models, etc.); register routes and policy.
- **New model** — `app/Core/Models/` (cross-cutting) or `app/Domains/<Name>/Models/`.
- **New Inertia page** — `resources/js/features/<name>/pages/`; backend must render that component name.
- **New feature flag** — Define in `app/Features/`, add to `config/features.php`, run `FeatureFlagSeeder`.

For full rules and step-by-step instructions, see [.cursor/rules/ARCHITECTURE.mdc](.cursor/rules/ARCHITECTURE.mdc) and [EXTENDING.md](EXTENDING.md).

## Code quality (run before submitting)

Please run these before opening a pull request:

- **PHP:** `vendor/bin/pint --test --format agent` and `vendor/bin/phpstan analyse`
- **Frontend:** `npm run format:check`, `npm run lint`, `npm run types`
- **Tests:** `php artisan test`

All of the above are also run in CI.

## Commits

Use [Conventional Commits](https://www.conventionalcommits.org/) so [semantic-release](https://github.com/semantic-release/semantic-release) can generate changelogs and versions:

- `feat: add something` — new feature (minor version bump)
- `fix: correct something` — bug fix (patch)
- `docs: update README` — documentation only
- `chore: update deps` — maintenance

Other common types: `refactor`, `test`, `style`, `ci`, `perf`. Use `BREAKING CHANGE:` in the footer for major changes.

## Pull requests

- Open PRs against the default branch (`main` or `master`).
- Describe what changed and why; reference any related issues.
- Ensure CI passes (lint, test, type checks).

## Code of conduct and security

- Please follow our [Code of Conduct](CODE_OF_CONDUCT.md).
- To report security vulnerabilities, see [SECURITY.md](SECURITY.md).
