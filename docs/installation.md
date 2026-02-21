---
title: Installation
layout: default
parent: Getting Started
nav_order: 1
description: 'Step-by-step installation for the Laravel React Boilerplate.'
---

# Installation

## Clone and backend setup

```bash
git clone https://github.com/hojabbr/boilerplate.git
cd boilerplate
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`: set `APP_NAME`, `APP_URL`, and `DB_*` at minimum. Optionally set `MEILISEARCH_*`, `REVERB_*`, etc. (see [Configuration](configuration.md)).

## Frontend setup

```bash
npm install
npm run build
```

## Database

```bash
php artisan migrate
```

Optional: `php artisan db:seed` to seed languages, settings, and optional content.

## With Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
cp .env.example .env
./vendor/bin/sail artisan key:generate
# Edit .env as needed
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
```

## Next steps

- [Configuration](configuration.md) — Environment variables and optional services.
- [Architecture](architecture.md) — Project structure and conventions.
