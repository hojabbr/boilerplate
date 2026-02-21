---
title: Search
layout: default
parent: Features
nav_order: 3
description: 'Laravel Scout and Meilisearch.'
---

# Search

## Setup

- **Laravel Scout** with **Meilisearch** driver. In `.env`: `SCOUT_DRIVER=meilisearch`, `MEILISEARCH_HOST`, `MEILISEARCH_KEY`.
- Mark searchable models with the Scout `Searchable` trait and implement `toSearchableArray()`.

## Adding a searchable model

1. Add an entry to `config/scout.php` under `meilisearch.index-settings` with **key = full model class name** (e.g. `\App\Domains\Blog\Models\BlogPost::class`). Value can be an array of Meilisearch options (e.g. `filterableAttributes`).
2. Implement `toSearchableArray()` on the model.
3. Run `php artisan scout:import "App\Domains\Blog\Models\BlogPost"` (or flush/sync) after schema or searchable-data changes.

Flush and re-import after changing searchable fields to avoid stale index state.
