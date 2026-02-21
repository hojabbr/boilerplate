---
title: Testing
layout: default
parent: Development
nav_order: 3
description: 'Pest, PHPUnit, Inertia asserts, Playwright, Jest/RTL.'
---

# Testing

## Backend (Pest + PHPUnit)

- **Location:** `tests/Feature/` and `tests/Unit/`.
- **Feature tests:** Use `assertInertia()->component('blog/Index')` etc.; the component path is resolved by `App\Core\Inertia\TestingViewFinder`. For feature-flagged routes, activate the flag in the test (e.g. `Feature::activate('blog')`).
- **Cache:** Use the model’s cache-key helpers in assertions (e.g. `Setting::siteCacheKey()`, `Page::slugCacheKey('slug')`) as in `tests/Feature/ContentCacheInvalidationTest.php`.
- **Run:** `php artisan test` or `php artisan test --compact --filter=TestName`.

## Frontend

- **Unit/component:** Jest + React Testing Library in `resources/js/__tests__/` (or project convention). Mock Zustand and i18next as needed.
- **E2E:** Playwright via the Pest browser plugin; run with the PHP test suite for consistency.

## Before submitting

Run `vendor/bin/pint --test --format agent`, `vendor/bin/phpstan analyse`, `npm run format:check`, `npm run lint`, `npm run types`, and `php artisan test`. These are also run in CI.
