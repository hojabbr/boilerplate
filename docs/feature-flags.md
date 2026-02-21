---
title: Feature flags
layout: default
parent: Features
nav_order: 2
description: 'Laravel Pennant; Filament toggleable vs Fortify.'
---

# Feature flags

## Laravel Pennant

Feature flags are defined in `app/Features/` and registered with `Feature::define()` in `AppServiceProvider`. Use `Feature::active('key')` in controllers and Filament to gate routes and UI.

## Toggleable in Filament

The **Feature flags** resource (Filament → Settings) lists only **blog**, **page**, and **contact-form**. These are in `config/features.php` under `toggleable`; seed with `FeatureFlagSeeder`. Adding a new toggleable feature: (1) feature class in `app/Features/`, (2) register in AppServiceProvider, (3) add key and label to `config/features.php`, (4) run FeatureFlagSeeder, (5) gate with `Feature::active()`.

## Registration and 2FA

**Registration** and **two-factor authentication** are controlled via **Laravel Fortify** configuration, not the Filament feature-flags list. Enable/disable them in Fortify config and views.
