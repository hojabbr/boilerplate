---
title: Feature flags
layout: default
parent: Features
nav_order: 2
description: 'Laravel Pennant; Filament toggleable (blog, page, contact-form, login, registration); 2FA via Fortify.'
---

# Feature flags

## Laravel Pennant

Feature flags are defined in `app/Features/` and registered with `Feature::define()` in `AppServiceProvider`. Use `Feature::active('key')` in controllers and Filament to gate routes and UI.

## Toggleable in Filament

The **Feature flags** resource (Filament → Settings) lists **blog**, **page**, **contact-form**, **login**, and **registration**. These are in `config/features.php` under `toggleable`; seed with `FeatureFlagSeeder`. Adding a new toggleable feature: (1) feature class in `app/Features/`, (2) register in AppServiceProvider, (3) add key and label to `config/features.php`, (4) run FeatureFlagSeeder, (5) gate with `Feature::active()`.

When **login** or **registration** is inactive, the corresponding routes return 404 (middleware `authFeatures`). The shared Inertia `features` object includes `login` and `registration` so the nav can hide or show the links.

## Two-factor authentication

**Two-factor authentication** is controlled via **Laravel Fortify** configuration, not the Filament feature-flags list. Enable/disable it in Fortify config and views.
