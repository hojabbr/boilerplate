---
name: react-i18next-inertia-laravel-localization
description: "Client‑side (React) internationalization using i18next and i18next‑browser‑languagedetector with a Laravel backend (using mcamara/laravel‑localization). Activates when building localization, language detection, dynamic UI translations, language switching, and when the user mentions i18n, locale, languages, translation files, or integration with Laravel Inertia."
license: MIT
metadata:
  author: community
---

# React i18next + i18next‑browser‑languagedetector + Inertia (Laravel + Laravel‑Localization)

## When to Apply

Activate this skill when:

- Building multilingual React frontends with **i18next**
- Detecting browser language automatically
- Integrating React with **Inertia.js** (using `@inertiajs/react`)
- Syncing frontend locale with a **Laravel backend** (e.g., `mcamara/laravel-localization`)
- Sending localized API requests to your backend
- Using translation JSON resource files

---

## Overview

This setup lets React handle **client‑side translations** with i18next while relying on Laravel’s localization system (e.g., mcamara/laravel‑localization) for backend routes and server‑side locale logic.  
Front‑end language detection (via `i18next-browser-languagedetector`) determines the initial language and can sync with server locale if desired.  [oai_citation:0‡GitHub](https://github.com/i18next/i18next-browser-languageDetector?utm_source=chatgpt.com)

---

## Key Libraries

| Library | Purpose |
|---------|---------|
| `i18next` | Core JS internationalization engine |
| `react-i18next` | React bindings for i18next |
| `i18next-browser-languagedetector` | Detects browser language preferences |
| `@inertiajs/react` | React adapter for Inertia.js apps |
| `laravel-localization` | Laravel backend localization & route prefixes  [oai_citation:1‡GitHub](https://github.com/mcamara/laravel-localization?utm_source=chatgpt.com) |

---

## Installation (Frontend)

Install required packages:

```bash
npm install i18next react-i18next i18next-browser-languagedetector @inertiajs/react

Also ensure React and Inertia helpers are installed:

npm install react react-dom

Inertia setup (React adapter):

npm install @inertiajs/react

Refer to Inertia docs for full setup.  ￼

⸻

i18next Setup

Create a Translation Init Module

Example: src/i18n.ts

import i18n from 'i18next';
import LanguageDetector from 'i18next-browser-languagedetector';
import { initReactI18next } from 'react-i18next';

i18n
  .use(LanguageDetector) // detect user language
  .use(initReactI18next) // bind to react-i18next
  .init({
    fallbackLng: 'en',
    supportedLngs: ['en', 'fr', 'de'],
    detection: {
      order: [
        'querystring',
        'localStorage',
        'cookie',
        'navigator',
        'htmlTag',
      ],
      caches: ['localStorage', 'cookie'],
    },
    interpolation: {
      escapeValue: false, // not needed for React
    },
    resources: {
      en: { translation: {/* english keys */} },
      fr: { translation: {/* french keys */} },
      de: { translation: {/* german keys */} },
    },
  });

export default i18n;

	•	LanguageDetector reads from query params, cookies, localStorage, etc.  ￼

⸻

Using useTranslation in React

In a React component:

import { useTranslation } from 'react-i18next';

export function Header() {
  const { t, i18n } = useTranslation();

  return (
    <>
      <h1>{t('welcome_message')}</h1>
      <button onClick={() => i18n.changeLanguage('fr')}>
        {t('switch_to_french')}
      </button>
    </>
  );
}

This updates UI text and lets you switch language dynamically.  ￼

⸻

Syncing with Laravel Backend

If your API or page routing uses localized URLs (e.g., example.com/de/...) courtesy of mcamara/laravel-localization, you can:
	1.	Detect language with i18next on client.
	2.	Include a query param or HTTP header (e.g., Accept-Language) to send current language with API calls.
	3.	Use Laravel’s localization middleware to serve localized content or route prefixes.  ￼

Example fetch with locale header:

fetch('/api/posts', {
  headers: {
    'Accept-Language': i18n.language,
  },
});


⸻

Inertia Integration

Inertia allows you to render backend data with React components while keeping routing and shared data on the Laravel side. To make localized pages:

Backend (Laravel Inertia)

use Inertia\Inertia;

Route::get('/', function () {
  return Inertia::render('Home', [
    'locale' => app()->getLocale(),
  ]);
});

Laravel then shares the locale with React via props.  ￼

Frontend (React Inertia)

import { usePage } from '@inertiajs/react';

export function App() {
  const { props } = usePage();
  const locale = props.locale;

  // Optionally sync i18next with locale from server
  useEffect(() => {
    i18n.changeLanguage(locale);
  }, [locale]);
}

This ensures that both front‑end and back‑end use the same locale context.

⸻

Storing & Persisting Language

The language detector can cache the preferred language in:
	•	LocalStorage
	•	Cookie
	•	Session Storage

This lets your app remember language preferences across sessions.  ￼

⸻

Routing Considerations

If using URL prefix localization (e.g., /en, /de) from Laravel:
	•	Detect language from URL on front end (e.g., using React Router or querystring) and pass it to i18next.
	•	Laravel localization middleware will handle correct redirects and backbone locale configuration.  ￼

⸻

Best Practices & Tips
	•	Keep translation JSON files consistent with backend language keys.
	•	Use fallbackLng to provide graceful fallbacks.
	•	Cache detected language preferences using localStorage or cookie.
	•	Always share server locale to the React app via Inertia props so both sides stay in sync.
	•	For larger apps, lazy‑load translation files using HTTP backend plugins.

⸻

Summary

Feature	Purpose
i18next	Core multi‑language engine
i18next‑browser‑languagedetector	Detects user locale from browser/environment
react‑i18next	React integration & hooks
@inertiajs/react	Integrates React with Laravel/Inertia
mcamara/laravel‑localization	Provides backend locale routing & helpers

---