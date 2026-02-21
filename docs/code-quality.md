---
title: Code quality
layout: default
parent: DevOps
nav_order: 2
description: 'Pint, ESLint, Prettier, Husky, Commitlint.'
---

# Code quality

## PHP

- **Laravel Pint** — Formatting and style. Run `vendor/bin/pint --format agent` to fix; CI runs `vendor/bin/pint --test --format agent`.
- **Larastan (PHPStan)** — Static analysis. Run `vendor/bin/phpstan analyse`. Configuration in `phpstan.neon` or `phpstan.php`.

## Frontend

- **ESLint** — Linting. Run `npm run lint` (fixes where applicable).
- **Prettier** — Formatting (with Tailwind and organize-imports plugins). Run `npm run format` to fix, `npm run format:check` to check.
- **TypeScript** — Run `npm run types` (tsc --noEmit).

## Pre-commit (Husky + lint-staged)

- **Husky** — Git hooks. On commit, **lint-staged** runs:
    - `*.{ts,tsx,js,jsx}`: eslint and prettier.
    - `*.{json,md,css}`: prettier.
    - `*.php`: Pint and PHPStan.

## Commits

- **Commitlint** — Enforces [Conventional Commits](https://www.conventionalcommits.org/) so semantic-release can generate changelogs and versions (e.g. `feat:`, `fix:`, `docs:`, `chore:`; `BREAKING CHANGE:` for major).
