# Feature modules

**Full frontend and extending docs:** [docs/frontend.md](../../../docs/frontend.md), [docs/extending.md](../../../docs/extending.md).

Feature-specific code lives under `resources/js/features/<name>/`. Create the `<name>/` directory when you add a new feature; add `pages/` (Inertia page components), `components/`, `hooks/`, `services/`, `types.ts`, and `index.ts` as needed (no `.gitkeep` or empty placeholders). Inertia resolves page components from `features/<name>/pages/` when the backend renders that name (e.g. `blog/Index` → `features/blog/pages/Index.tsx`); the `welcome` page lives under `features/landing/pages/welcome.tsx`.

- **components/** — UI scoped to this feature (e.g. BlogPostCard, ContactForm).
- **hooks/** — Feature-specific hooks (e.g. useBlogPosts, useContactForm).
- **services/** — Feature-level services (e.g. submit contact form via Inertia).
- **types.ts** — TypeScript types for the feature.
- **index.ts** — Public exports for the feature.

Use these modules from Inertia pages: `import { BlogPost } from '@/features/blog'` or `import { ... } from '@/features/blog/components'`.

Shared UI stays in `components/ui/` and `components/common/`.

## Adding a new feature

Create `resources/js/features/<name>/` and add `pages/`, `components/`, `hooks/`, `services/`, `types.ts`, `index.ts` as needed. Ensure the backend has a route that renders an Inertia component name like `<name>/PageName` (e.g. `blog/Index`), or a single-segment name like `welcome` mapped in `app.tsx` for the landing page.

## Adding a new Inertia page

Create a file under `features/<name>/pages/` (e.g. `Show.tsx`). The backend must call `Inertia::render('<name>/Show', ...)` (or the matching component name). Use Wayfinder-generated route/action helpers: import from `@/routes` or `@/actions` for links and form submissions.

## When to check docs

- **Inertia** (forms, navigation, SSR, deferred props) — Inertia 2 docs.
- **React** (components, hooks) — React 19 docs.
- **Routes/actions from backend** — Laravel Wayfinder.
- **UI components** — Shadcn and Tailwind v4.
- **i18n** — i18next.
