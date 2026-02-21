---
title: CI/CD
layout: default
parent: DevOps
nav_order: 1
description: 'GitHub Actions workflow: lint, test, type check, optional release.'
---

# CI/CD

The repository uses **GitHub Actions** (`.github/workflows/ci.yml`) for CI.

## Triggers

- Push and pull requests to `main` or `master`.

## Jobs

**lint**

- Setup PHP 8.5 and Node 24.
- Install Composer and npm dependencies.
- Run **Pint** (`vendor/bin/pint --test --format agent`).
- Run **PHPStan** (`vendor/bin/phpstan analyse --no-progress`).
- Run **frontend format check** (`npm run format:check`).
- Run **frontend lint** (`npm run lint`).
- Generate Wayfinder routes (copy `.env.example`, key:generate, wayfinder:generate).
- Run **type check** (`npm run types`).

**test**

- Uses SQLite in-memory (`DB_CONNECTION: sqlite`, `DB_DATABASE: ":memory:"`).
- Install dependencies, copy `.env`, key:generate, `npm run build`, `php artisan test --compact`.

**release** (optional)

- Runs on push to `main`/`master` when lint and test pass, unless the commit message contains `skip release`.
- Runs **semantic-release** (version bump, changelog, Git tag, GitHub release) using `GITHUB_TOKEN`. Requires Conventional Commits.

## Environment

- `PHP_VERSION`: 8.5
- `NODE_VERSION`: 24
