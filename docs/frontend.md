---
title: Frontend
layout: default
parent: Architecture
nav_order: 3
description: 'Features, pagePath, layouts, UI components, motion, and state.'
---

# Frontend

## Stack

React 19, Inertia 2, Vite, TypeScript, Tailwind CSS v4, Shadcn/Radix, i18next, Zustand, Motion (LazyMotion + domAnimation). Path alias `@/` → `resources/js`.

## Inertia pages and resolution

- Pages live in `resources/js/features/<name>/pages/` (e.g. `blog/Index` → `features/blog/pages/Index.tsx`). The backend renders a component name like `blog/Index` or `welcome`; `app.tsx` and `ssr.tsx` use a shared `pagePath()` and glob to resolve it. Single-segment name `welcome` maps to `features/landing/pages/welcome.tsx`.
- PHP tests use `App\Core\Inertia\TestingViewFinder` so `assertInertia()->component('blog/Index')` finds the correct file.

## Layouts and components

- **Layouts** — `resources/js/layouts/` (auth, app, settings, public).
- **components/ui/** — Shadcn primitives (Button, Input, Card, etc.). Prefer these for all interactive UI.
- **components/common/** — Shared compositions (NavSearch, language switcher, SeoHead, motion presets). Feature-specific UI in `features/<name>/components/`.

## Theme and i18n

- Single source of truth for appearance: `useAppearance` (light/dark/system), persisted via cookie and localStorage. Language switcher uses shared `locale_switch_urls` from the backend.
- RTL: set `dir` on the document from locale; use logical spacing (`ps-*`, `pe-*`) and direction-aware icons.

## Motion

- Page/section animation config in `components/common/motion-presets.ts` (pageEnter, fadeInUp, fadeInUpView). Use `m` from motion/react with these presets; use Tailwind enter/exit for Shadcn overlays.

## Adding a new page

1. Add a file under `resources/js/features/<name>/pages/` (e.g. `Show.tsx`).
2. Backend must call `Inertia::render('<name>/Show', ...)`. Run `npm run build` (or dev) after adding routes so Wayfinder stays in sync.
