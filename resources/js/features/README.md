# Feature modules

Feature-specific code lives under `resources/js/features/<name>/`:

- **components/** — UI scoped to this feature (e.g. BlogPostCard, ContactForm).
- **hooks/** — Feature-specific hooks (e.g. useBlogPosts, useContactForm).
- **services/** — Feature-level services (e.g. submit contact form via Inertia).
- **types.ts** — TypeScript types for the feature.
- **index.ts** — Public exports for the feature.

Use these modules from Inertia pages: `import { BlogPost } from '@/features/blog'` or `import { ... } from '@/features/blog/components'`.

Shared UI stays in `components/ui/` and `components/common/`.
