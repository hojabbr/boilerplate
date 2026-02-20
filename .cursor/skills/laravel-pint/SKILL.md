---
name: laravel-pint
description: 'Formats and enforces consistent PHP code style in Laravel projects using Laravel Pint. Activates when fixing code style, running formatting checks, adding CI linting steps, creating or updating `pint.json`, or when the user mentions code style, formatting, lint, or static analysis.'
license: MIT
metadata:
    author: Laravel
---

# Laravel Pint (Code Style Formatter)

## When to Apply

Activate this skill when:

- Formatting PHP code to follow a consistent style
- Setting up code style linting as part of CI pipelines
- Running automated code formatting before commits
- Customising code style rules via a config file
- Creating or maintaining a `.pint.json` file
- Troubleshooting style violations in Laravel code

## Documentation

Use `search-docs` to open the **Laravel Pint** section of the Laravel 12.x documentation.  
Laravel Pint is an **opinionated PHP code style fixer** built on top of **PHP‑CS‑Fixer** that helps keep code clean and consistent without heavy configuration. [oai_citation:1‡laravel.su](https://laravel.su/docs/12.x/pint)

---

## Introduction

Laravel Pint provides a minimal, consistent set of formatting rules for PHP projects.  
It’s included by default in new Laravel applications and requires **no configuration** to get started. [oai_citation:2‡laravel.su](https://laravel.su/docs/12.x/pint)

---

## Installation

Pint comes bundled with Laravel 12, but if it’s missing or you’re adding it manually:

```bash
composer require laravel/pint --dev
```

Once installed, a pint binary becomes available in your project’s vendor/bin directory. ￼

⸻

Running Pint

Basic Formatting

To automatically fix code style issues across the project:

./vendor/bin/pint

Pint reports which files were modified by the formatter. ￼

Run on Specific Files or Directories

./vendor/bin/pint app/Models
./vendor/bin/pint app/Http/Controllers/UserController.php

You can target exactly what you want to format. ￼

⸻

Useful Options

Show Verbose Output

Show detailed change information:

./vendor/bin/pint -v

Test Mode (Dry‑run)

Check code style without applying changes:

./vendor/bin/pint --test

Pint will exit with a non‑zero status if issues are found. ￼

Parallel Mode (Speed)

Run formatting in parallel (faster on large codebases):

./vendor/bin/pint --parallel --max-processes=4

This uses multiple cores for performance. ￼

⸻

Configuring Pint

By default, Pint uses an opinionated Laravel preset that requires no configuration.
To customize formatting rules, create a pint.json file in your project root:

{
"preset": "laravel"
}

You may set a different preset (e.g., psr12, symfony, empty) or define custom rules.
You can also supply a config file explicitly:

./vendor/bin/pint --config path/to/pint.json

Custom presets and rules help fit your team’s code style preferences. ￼

⸻

Presets & Rules

Presets

Presets are groups of formatting rules. Typical presets include:
• laravel (default)
• psr12
• symfony
• empty

Presets define a base style Pint will follow. ￼

Custom Rules

Inside pint.json, you can enable, disable, or configure individual rules.
For example:

{
"preset": "laravel",
"rules": {
"array_indentation": true,
"no_unused_imports": true
}
}

This lets you fine‑tune the formatter behavior. ￼

⸻

Excluding Files & Folders

You can exclude patterns and paths using configuration options:

{
"exclude": [
"storage",
"tests/Fixtures"
],
"notName": ["*Test.php"],
"notPath": ["app/Legacy/*"]
}

This prevents Pint from formatting unwanted files. ￼

⸻

Continuous Integration (CI)

Integrate Pint into CI pipelines to enforce style consistency:
• GitHub Actions can run Pint on push or pull requests.
• Pre‑commit hooks can auto‑format code locally before commits.

Automating formatting ensures consistent results across teams. ￼

⸻

Summary

Feature Purpose
Included by default Laravel 12 includes Pint out of the box
Automatic formatting Apply style fixes with a single command
Test/Dry‑run Validate style without modifying files
Presets Built‑in and custom code style collections
Configurable rules Customize behavior via pint.json
CI & workflows Enforce code style in pipelines

---
