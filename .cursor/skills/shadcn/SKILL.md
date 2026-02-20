---
name: shadcn‑ui‑architecture
description: 'Defines how to integrate and structure **shadcn/ui** components in React + Tailwind applications. Focuses on usage conventions, state handling, accessibility, extension patterns, and component ownership best practices suited for full‑stack apps like your RILT stack.'
license: MIT
metadata:
    author: your‑team
---

# shadcn/ui — Component System Architecture

## 🧩 Purpose

**shadcn/ui** is not a typical opaque UI package — it’s a _source‑first component system_ that provides accessible, composable, and Tailwind‑based UI primitives directly in your codebase. Unlike bundled libraries, it **copies source files into your project**, giving full control over the components. ([turn0search15](https://ui.shadcn.com/docs))

---

## 📦 Core Principles

### ✅ Open‑Code by Design

shadcn/ui components live in your project as actual source files. You can read, understand, compose, and extend them because they are _your code_. This enables AI tooling and customization. ([turn0search15](https://ui.shadcn.com/docs))

### 🧱 Composition Over Copy‑Paste

Each shadcn component is built on composable interfaces, making it predictable and easy to combine with Tailwind and other primitives. ([turn0search15](https://ui.shadcn.com/docs))

---

## 📁 Recommended Directory Structure

components/
├── ui/ # Base shadcn/ui primitives (auto‑generated)
│ ├── button.tsx
│ ├── input.tsx
│ ├── tooltip.tsx
│ └── …
├── common/ # App‑level shared UI wrappers
│ ├── IconButton.tsx
│ └── FormField.tsx
├── layout/ # Layout patterns (Nav, Sidebar, Footer)
├── feature/ # Feature‑scoped components
└── data/ # UI logic wrappers and helpers

🛑 **Never modify the base `components/ui/` folder manually.** These are shadcn primitives that can be updated via CLI, and modifications can be lost or conflict when regenerating components. Instead, _extend and compose_ them in your own folders (`common/`, `feature/`, etc.). ([turn0search5](https://claude-plugins.dev/skills/%40JewelsHovan/pain-plus-site/shadcn-ui-best-practices), [turn0search16](https://go.lightnode.com/tech/shadcn-ui))

---

## 🛠 Adding & Extending Components

### 💡 Use the CLI

Add components using the official CLI to ensure correct paths and behavior:

```bash
pnpm dlx shadcn@latest add <component>
```

This keeps the component within components/ui/ and avoids misconfigurations. (turn0search0￼)

⸻

🔁 Wrap & Extend Instead of Editing

If you need custom behavior or variants: 1. Wrap the base component:

import { Button } from "@/components/ui/button";

export function PrimaryActionButton(props) {
return <Button className="bg-primary text-white" {...props} />;
}

    2.	Compose new logic outside ui/
    •	Put new behaviors in components/common/ or components/feature/
    •	Keep ui/ as pristine primitives

This ensures upgrade safety and reduces merge conflicts when regenerating or updating components. (turn0search5￼)

⸻

🧠 Tooltip Component Usage

A new tooltip component exists in shadcn/ui:
• Displays contextual information on hover or focus
• Built with Radix UI over Tailwind
• Requires wrapping your app with TooltipProvider to work correctly

Example in app/layout.tsx:

import { TooltipProvider } from "@/components/ui/tooltip"

export default function RootLayout({ children }: { children: React.ReactNode }) {
return (

<html lang="en">
<body>
<TooltipProvider>{children}</TooltipProvider>
</body>
</html>
)
}

After that, use:

import { Tooltip, TooltipTrigger, TooltipContent } from "@/components/ui/tooltip"

to place tooltips anywhere. (turn0search0￼)

⸻

🧠 Accessibility & Design
• rad shadcn/ui is built on Radix primitives, so focus, ARIA roles, and keyboard navigation are supported.
• When extending or wrapping, maintain accessibility semantics — do not break ARIA behaviors.
• Favor Tailwind utility styling instead of injecting behavior logic into primitives. (turn0search22￼)

⸻

🚀 RTL & Theming
• Base UI supports RTL if enabled — classes adapt with the configured CLI schema.
• Use Tailwind’s logical utilities (start, end, etc.) for consistent direction support.
• Abstractions for themes should live in higher‑level components, not in base UI. (turn0search9￼)

⸻

🧪 Testing Practices

When testing UI components:
• Test your composed components (components/common/, components/feature/) not base primitives.
• Render extended components with RTL/jest/Playwright to validate accessibility and signal behavior.
• Avoid asserting internal implementation of primitives; assert visible outcomes.

⸻

📌 Summary Rules

Rule Guideline
Base UI components/ui/ — Do NOT modify manually
Custom Components Compose in common/ or feature/
Adding Components Always use shadcn@latest add
Tooltip Setup Wrap root with TooltipProvider
Accessibility Preserve ARIA semantics
Theming/RTL External wrappers handle logic
