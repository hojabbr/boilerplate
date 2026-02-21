---
title: Configuration
layout: default
parent: Getting Started
nav_order: 2
description: 'Environment variables and optional services for the Laravel React Boilerplate.'
---

# Configuration

Configure the application via `.env` (copy from `.env.example`).

## Required

| Variable        | Description                                                                         |
| --------------- | ----------------------------------------------------------------------------------- |
| `APP_NAME`      | Application name                                                                    |
| `APP_URL`       | Full URL (e.g. `http://localhost`)                                                  |
| `DB_CONNECTION` | Database driver (e.g. `mysql`, `sqlite`)                                            |
| `DB_*`          | Database host, database name, username, password (or `DB_DATABASE` for SQLite path) |

## Optional services

| Variable           | Description                                  |
| ------------------ | -------------------------------------------- |
| `SCOUT_DRIVER`     | `meilisearch` for full-text search           |
| `MEILISEARCH_HOST` | e.g. `http://127.0.0.1:7700`                 |
| `MEILISEARCH_KEY`  | Meilisearch API key                          |
| `REVERB_*`         | Laravel Reverb (WebSockets)                  |
| `VITE_APP_NAME`    | Shown in frontend (defaults from `APP_NAME`) |

See `.env.example` in the repository for the full list and defaults.
