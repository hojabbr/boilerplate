---
title: Architecture
layout: default
nav_order: 2
description: 'Backend and frontend structure of the Laravel React Boilerplate.'
---

# Architecture

The boilerplate uses a **domain-based backend** (Core + Domains) and **feature-based frontend** (Inertia pages under `resources/js/features/`).

- **[Overview](overview.md)** — High-level structure and directory map.
- **[Backend](backend.md)** — Core, Domains, models, jobs, policies, no global `app/Models/`.
- **[Frontend](frontend.md)** — Features, pagePath resolution, layouts, UI components, motion, state.
