---
title: Documentation index
layout: default
parent: Reference
nav_order: 2
description: 'List of all documentation files and their purpose. For discovery and AI/crawlers.'
---

# Documentation index

All documentation files in this site and a one-line description of each. Use this for discovery and context.

| File                   | Description                                                                          |
| ---------------------- | ------------------------------------------------------------------------------------ |
| `index.md`             | Documentation home: overview, quick start, documentation map                         |
| `getting-started.md`   | Section: Installation and Configuration                                              |
| `installation.md`      | Step-by-step installation (Composer, npm, Sail, migrations)                          |
| `configuration.md`     | Environment variables and optional services                                          |
| `architecture.md`      | Section: Architecture (Overview, Backend, Frontend)                                  |
| `overview.md`          | High-level directory structure and architecture diagram                              |
| `backend.md`           | Core, Domains, models, jobs, conventions, ide-helper                                 |
| `frontend.md`          | Features, pagePath, layouts, UI, theme, motion, adding pages                         |
| `features.md`          | Section: Localization, Feature flags, Search, Admin                                  |
| `localization.md`      | mcamara, route prefixes, RTL, i18next sync                                           |
| `feature-flags.md`     | Pennant, Filament toggleable (blog, page, contact-form), Fortify (registration, 2FA) |
| `search.md`            | Scout + Meilisearch, adding searchable models                                        |
| `admin.md`             | Filament 5, resources, permissions, Lara Zeus translatable                           |
| `development.md`       | Section: Extending, Scaffolding, Testing                                             |
| `extending.md`         | How to add domains, models, pages, feature flags, Filament; convention table         |
| `scaffolding.md`       | boilerplate:domain, boilerplate:locale; options, rollback, tests                     |
| `testing.md`           | Pest, PHPUnit, Inertia asserts, Playwright, Jest/RTL                                 |
| `devops.md`            | Section: CI/CD, Code quality                                                         |
| `ci.md`                | GitHub Actions: lint, test, type check, optional semantic-release                    |
| `code-quality.md`      | Pint, ESLint, Prettier, Husky, Commitlint                                            |
| `reference-section.md` | Section: Reference (this reference and doc index)                                    |
| `reference.md`         | Paths, config & env: package versions, key paths, config files, env vars, commands   |
| `doc-index.md`         | This file: list of all doc files and one-line descriptions                           |

**Terminology:** _Domain_ = vertical slice under `app/Domains/<Name>` (e.g. Blog, Page). _Core_ = cross-cutting code in `app/Core/`. _Feature_ (frontend) = module under `resources/js/features/<name>/`. _Feature flag_ (Pennant) = definition in `app/Features/` and optional toggle in Filament.
