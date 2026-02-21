---
title: Backend
layout: default
parent: Architecture
nav_order: 2
description: 'Core, Domains, models, jobs, and conventions. No app/Models/.'
---

# Backend

## Core vs Domains

- **Core** (`app/Core/`) — Cross-cutting: Middleware, Models (Language, Setting, FeatureFlag), Observers, Policies, Providers, Services (PagePropsService), Contracts, Exceptions, Traits. **Core/Inertia/** contains `TestingViewFinder` for resolving Inertia page component names in PHP tests.
- **Domains** (`app/Domains/<Name>/`) — Vertical slices: Auth, Blog, Contact, Dashboard, Landing, Page, Profile, Search. Each may have Http/Controllers, Http/Requests, Models, Observers, Policies, Queries, Services, Actions, DTOs, Search, Jobs/.

There is **no** global `app/Models/` or `app/Jobs/`; models live in `Core/Models/` or `Domains/<Name>/Models/`, jobs in `Domains/<Name>/Jobs/` or `Core/Jobs/`.

## Controllers and requests

- Controllers live in `Domains/<Name>/Http/Controllers/` and extend `App\Http\Controllers\Controller`. They are thin: call Queries/Services/Actions, merge with PagePropsService when needed, return `Inertia::render(...)`.
- FormRequests live in `Domains/<Name>/Http/Requests/`. Routes in `routes/web.php` point to domain controllers (e.g. `App\Domains\Blog\Http\Controllers\BlogController`).

## Models and IDE Helper

- Add new model paths to `config/ide-helper.php` → `model_locations` (e.g. `app/Domains/Product/Models`). Run `php artisan ide-helper:models -M` after adding or moving models.

## Soft deletes and cascading

- CMS and domain models use `SoftDeletes`. Cascaded soft deletes via observers/events; cascaded force deletes via DB foreign keys and observers. Document parent/child and force-delete behavior when adding relations (see ARCHITECTURE in the repo).

## Adding a new domain

1. Create `app/Domains/<Name>/` with Http, Models, etc. as needed.
2. Register routes (in the locale group in `routes/web.php` or `routes/settings.php`) and register the policy in `AppServiceProvider::registerPolicies()`.
3. If the domain has models: add the path to `config/ide-helper.php` and run `ide-helper:models -M`.

Or use `php artisan boilerplate:domain` to scaffold (see [Scaffolding](scaffolding.md)).
