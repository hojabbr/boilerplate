---
title: Documentation
layout: default
nav_order: 0
description: 'Documentation for the Laravel React Boilerplate — production-ready starter for Laravel 12, Inertia 2, React 19.'
---

# Laravel React Boilerplate

A **production-ready starter** for **Laravel 12**, **Inertia 2**, and **React 19** with localization, Filament admin, feature flags, full-text search, and real-time support. Use it as a template or clone to build full-stack apps without redoing auth, i18n, theme, or tooling.

**Repository:** [github.com/hojabbr/boilerplate](https://github.com/hojabbr/boilerplate)

---

## What's in this documentation

- **[Getting Started](getting-started.md)** — Installation, configuration, and first run (including Sail).
- **[Architecture](architecture.md)** — Backend (Core, Domains) and frontend (features, layouts, UI) structure.
- **[Features](features.md)** — Localization, feature flags, search, and the Filament admin panel.
- **[Development](development.md)** — How to extend the boilerplate, use scaffolding commands, and run tests.
- **[DevOps](devops.md)** — CI/CD, code quality (Pint, ESLint, Prettier), and releases.
- **[Reference](reference-section.md)** — Paths, config, env vars, and [documentation index](doc-index.md).

## Quick start

1. Clone the boilerplate: `git clone https://github.com/hojabbr/boilerplate.git` then run `composer install`, `cp .env.example .env`, `php artisan key:generate`, `npm install`, `npm run build`, and `php artisan migrate`.
2. Or use [Laravel Sail](https://laravel.com/docs/sail): `./vendor/bin/sail up -d` and run the same steps inside the container.
3. See [Installation](installation.md) for detailed steps and [Configuration](configuration.md) for environment variables.

## Documentation map

| Section         | Pages                                                                                                                      |
| --------------- | -------------------------------------------------------------------------------------------------------------------------- |
| Getting Started | [Installation](installation.md), [Configuration](configuration.md), [Directory structure](overview.md#directory-structure) |
| Architecture    | [Overview](overview.md), [Backend](backend.md), [Frontend](frontend.md)                                                    |
| Features        | [Localization](localization.md), [Feature flags](feature-flags.md), [Search](search.md), [Admin](admin.md)                 |
| Development     | [Extending](extending.md), [Scaffolding](scaffolding.md), [Testing](testing.md)                                            |
| DevOps          | [CI/CD](ci.md), [Code quality](code-quality.md)                                                                            |
| Reference       | [Paths, config & env](reference.md), [Documentation index](doc-index.md)                                                   |
