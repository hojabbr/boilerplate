---
title: DevOps
layout: default
nav_order: 5
description: 'CI/CD, code quality, and releases.'
---

# DevOps

- **[CI/CD](ci.md)** — GitHub Actions: lint, test, type checks.
- **[Code quality](code-quality.md)** — Pint, ESLint, Prettier, Husky, Commitlint.
- **Queues & Horizon** — Use Redis (`QUEUE_CONNECTION=redis`) and Laravel Horizon for background jobs (e.g. blog AI generation, notifications). Run `php artisan horizon` in production so the `default` and `blog` queues are processed.

Optional: **semantic-release** for version bumps, changelog, and GitHub releases driven by Conventional Commits.
