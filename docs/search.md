---
title: Search
layout: default
parent: Features
nav_order: 3
description: 'Laravel Scout and Meilisearch.'
---

# Search

## Setup

- **Laravel Scout** with **Meilisearch** driver.
- In `.env`: `SCOUT_DRIVER=meilisearch`, `MEILISEARCH_HOST`, `MEILISEARCH_KEY` (optional).
- **Queue and transactions** (recommended for production):
    - `SCOUT_QUEUE=true` — Indexing runs in the queue so requests are not blocked. Ensure Horizon or queue workers are running. Use `SCOUT_QUEUE=false` for local sync (indexing runs in the same request).
    - `SCOUT_AFTER_COMMIT=true` — Index sync runs only after the database transaction commits, so rolled-back data is never indexed.
- Mark searchable models with the Scout `Searchable` trait and implement `toSearchableArray()` (and optionally `shouldBeSearchable()`).

## Adding a searchable model

1. Add an entry to `config/scout.php` under `meilisearch.index-settings` with **key = full model class name** (e.g. `\App\Domains\Blog\Models\BlogPost::class`). Value is an array of Meilisearch options (e.g. `filterableAttributes`).
2. Implement `toSearchableArray()` on the model. Implement `shouldBeSearchable()` if only some records should be searchable (e.g. active pages, published posts).
3. Run `php artisan scout:sync-index-settings` after changing filterable attributes so Meilisearch gets the new settings.
4. Run `php artisan scout:import "App\Domains\Blog\Models\BlogPost"` (or flush then import) after schema or searchable-data changes.

Flush and re-import after changing searchable fields to avoid stale index state.

## Keeping the index in sync

- **Normal create/update/delete/restore/force delete:** Scout’s model observer syncs the index automatically (queued or sync depending on `SCOUT_QUEUE`).
- **Bulk updates:** `Model::where(...)->update([...])` does **not** fire model events. After bulk updates that affect searchable state (e.g. deactivating pages), run `php artisan scout:import "ModelClass"` for each affected model, or use `scout:sync-all` if implemented.

## Migrations

- **After schema or data migrations** that affect searchable data or filterable attributes:
    1. Run `scout:sync-index-settings` if you changed `filterableAttributes` (or other index settings) in `config/scout.php`.
    2. Run `scout:import "ModelClass"` for each searchable model whose data or schema changed.
- **Deployment order:** (1) Run migrations, (2) run `scout:sync-index-settings` if index settings changed, (3) run `scout:import` for affected models (or `scout:sync-all`).
- **Data migrations / backfills** (e.g. `DB::table()->update()`, raw SQL, or `Model::withoutEvents()`): No model events fire. Run `scout:import` for the affected model(s) after the migration or backfill.
- **Edge cases:**
    - **New column in toSearchableArray():** Add to config if filterable, run `scout:sync-index-settings`, then `scout:import`.
    - **Column removed/renamed:** Update model and config, run `scout:sync-index-settings`; then `scout:flush` and `scout:import` for a clean index.
    - **Zero-downtime backfill:** Run `scout:import` after the backfill completes.
    - **Migration rollback:** After rollback, run `scout:flush` for the model and then `scout:import` to repopulate from the current schema, or accept a temporary mismatch until the next full import.

## Seeding

| Seeder writes with            | Model events | Scout syncs automatically? | Action after seed                          |
| ----------------------------- | ------------ | -------------------------- | ------------------------------------------ |
| `create()` / `save()`         | Yes          | Yes (sync if queue off)    | If queue on: run workers or `scout:import` |
| `insert()` / `upsert()` / raw | No           | No                         | Run `scout:import` (or `scout:sync-all`)   |

- **`migrate:fresh --seed`:** If seeders use only `create()`/`save()` and `SCOUT_QUEUE=false`, the index is in sync after the command. If seeders use `insert()`/`upsert()` or you use queued indexing, run `scout:import` (or `scout:sync-all`) after the command.
- **New developer / fresh clone:** After `migrate --seed` or `migrate:fresh --seed`, run `scout:import` for Page and BlogPost (or `scout:sync-all`) unless seeders use only `create()`/`save()` with `SCOUT_QUEUE=false`.
- **CI:** Use `SCOUT_DRIVER=collection` in tests so no Meilisearch is required and no post-seed import is needed; or run `scout:import` after seeding when using Meilisearch.

## Commands

- **`scout:import "ModelClass"`** — Import all searchable records for the model into the index (e.g. after schema changes, bulk updates, or seeding with insert/upsert).
- **`scout:flush "ModelClass"`** — Remove all records for the model from the index. Follow with `scout:import` to rebuild.
- **`scout:sync-index-settings`** — Push index settings from `config/scout.php` (e.g. `filterableAttributes`) to Meilisearch. Run after changing filterable or other index settings.
- **`scout:sync-all`** (optional, custom) — Run `scout:import` for all configured searchable models in one command.

## Queue and transactions

- **SCOUT_QUEUE:** When `true`, indexing is queued (recommended in production so requests are fast). When `false`, indexing runs synchronously (useful for local dev or tests).
- **SCOUT_AFTER_COMMIT:** When `true`, indexing runs only after the DB transaction commits, so data from rolled-back transactions is never indexed. Recommended for production.
