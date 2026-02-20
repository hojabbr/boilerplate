---
name: pest-plugin-browser
description: 'Adds browser‑level end‑to‑end testing to your PHP test suite via Pest and Playwright. Activates when writing browser or E2E tests, navigating pages, interacting with elements, asserting UI and accessibility, using screenshots or debugging, or when the user mentions browser testing, Playwright, click(), visit(), UI tests, or Laravel functional tests.'
license: MIT
metadata:
    author: Pest Team
---

# Pest Browser Plugin (Browser Testing with Pest)

## When to Apply

Activate this skill when:

- Writing end‑to‑end **browser tests** using Pest
- Navigating to URLs with `visit()`
- Interacting with UI elements (`click`, `type`, etc.)
- Running Playwright‑powered tests
- Taking screenshots or debugging browser tests
- Running tests in parallel with `--parallel`
- Working with Laravel features in browser tests (`RefreshDatabase`, authentication)
- Asserting UI behavior, navigation, and accessibility

This plugin is the official browser testing extension for Pest. [oai_citation:1‡GitHub](https://github.com/pestphp/pest-plugin-browser)

## Documentation

Use `search-docs` (e.g., `browser-testing pest`) to open the **Browser Testing** section of Pest’s official docs.  
The plugin enables modern browser testing through Playwright integration, providing fluent, expressive testing APIs. [oai_citation:2‡pestphp.com](https://pestphp.com/docs/browser-testing)

---

## Installation

Install the plugin via Composer:

```bash
composer require pestphp/pest-plugin-browser --dev
```

Then install the Playwright dependency and browsers:

npm install playwright@latest
npx playwright install

Add screenshot folders to .gitignore if needed (e.g., tests/Browser/Screenshots). ￼

⸻

Basic Browser Tests

Use visit() to navigate a page and interact with it:

it('may welcome the user', function () {
$page = visit('/');
$page->assertSee('Welcome');
});

This performs a real browser visit and checks visible text. ￼

⸻

Interacting With Pages

Browser tests can interact with elements through methods like:
• $page->click('text or selector')
• $page->type('selector', 'value')
• $page->press('button')
• $page->fill('selector', 'value')
• $page->select('selector', 'value')
• $page->attach('fileSelector', 'path/to/file')

And more — these methods map to real browser actions powered by Playwright. ￼

⸻

Assertions

Pest browser tests include rich assertions such as:
• $page->assertSee('text') / $page->assertDontSee('text')
• $page->assertUrlIs('/path')
• $page->assertNoConsoleLogs()
• $page->assertNoJavaScriptErrors()
• $page->assertNoAccessibilityIssues()

These let you validate UI state, navigation, and accessibility. ￼

⸻

Advanced Usage

Browsers & Devices

Specify different browsers or devices:

./vendor/bin/pest --browser firefox

Or in Pest.php:

pest()->browser()->inFirefox();
pest()->browser()->on()->iPhone14Pro();

You can also use dark/light modes or set viewport sizes. ￼

⸻

Navigation & Interaction

You can navigate between pages, locate elements, and work with advanced interactions:
• $page->navigate('/other');
• $page->hover(selector);
• $page->drag(selector, selector2);
• $page->wait(seconds);

These give you control similar to traditional browser automation tools. ￼

⸻

Screenshots & Debugging

Capture screenshots for debugging:

$page->screenshot(); 
$page->screenshotElement('#element');

Use --debug to open the browser headed and pause on failures, or call $page->debug() inside a test. ￼

⸻

Running Browser Tests

Browser tests run like regular Pest tests:

./vendor/bin/pest

For speed, run in parallel:

./vendor/bin/pest --parallel

For detailed output or debugging:

./vendor/bin/pest --debug

You can also use --headed to see the browser UI during testing. ￼

⸻

Tips & Best Practices
• Keep browser tests isolated for predictable CI results.
• Use Laravel testing features (RefreshDatabase, factories) with browser tests.
• Tag long or UI‑heavy tests to run selectively in CI.
• Capture screenshots for failed tests to aid debugging.

⸻

Summary

Feature Purpose
Browser Plugin Adds browser testing (pest-plugin-browser)
Navigation visit(), navigate()
Interaction click, type, hover, submit
Assertions Check UI text, URLs, consoles, accessibility
Browsers Chrome, Firefox, Safari options
Debugging Screenshots, paused headed mode
Parallel Tests Faster test runs

---
