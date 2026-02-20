---
name: laravel-localization
description: 'Enables easy multilingual route and URL localization using the mcamara/laravel-localization package. Activates when adding localized routes, language switching, generating localized URLs, handling locale redirects, defining supported locales, or when the user mentions i18n, locale, multilanguage, translated routes, language selector, or SEO for languages.'
license: MIT
metadata:
    author: mcamara
---

# mcamara/laravel-localization for Laravel

## When to Apply

Activate this skill when:

- Adding multilingual route prefixes and locale management
- Defining supported locales for your application
- Creating localized URLs and language selectors
- Detecting or redirecting based on browser language
- Translating routes (different URL segments per locale)
- Applying localization middleware
- Handling locale persistence (session/cookie)

## Documentation

Use `search-docs` to open the package repository and official README.

This package offers localized routing, automatic language detection, smart redirects, localized helpers, and translatable routes. [oai_citation:1‡GitHub](https://github.com/mcamara/laravel-localization)

## Installation

### 1. Install via Composer

```bash
composer require mcamara/laravel-localization
```

This installs the package into your Laravel project. ￼

⸻

Configuration

2. Publish Config

Publish the config file so you can customize settings:

php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"

This creates the config/laravellocalization.php file where you can configure:
• supportedLocales: languages your app supports
• useAcceptLanguageHeader: auto detect language via browser
• hideDefaultLocaleInURL: hide default locale prefix
• localesOrder: custom order for languages
• localesMapping: map locale codes to custom segments
• urlsIgnored: exclude specific URLs from localization ￼

⸻

Middleware

3. Register Middleware

Add the localization middleware in app/Http/Kernel.php:

protected $middlewareGroups = [
'web' => [
\Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
\Mcamara\LaravelLocalization\Middleware\LocalizationRedirect::class,
\Mcamara\LaravelLocalization\Middleware\LocaleViewPath::class,
],
];

Using this middleware enables:
• Session/cookie storage of chosen locale
• Redirects when locale is missing
• View path switching based on locale ￼

⸻

Localized Routes

4. Wrap Routes in Localized Group

Wrap your routes so they respect locale prefixes:

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
'prefix' => LaravelLocalization::setLocale(),
'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
Route::get('/', function () {
return view('welcome');
});
});

This will produce URLs like:
• /en
• /es
• /fr
(and so on for each supported locale) ￼

⸻

Helpers

5. Localization Helpers

Get Localized URL
Return the URL adapted to a locale:

{{ LaravelLocalization::getLocalizedURL('es') }}

Generates a URL that includes the locale prefix. ￼

⸻

Get Supported Locales

{{ LaravelLocalization::getSupportedLocales() }}

Returns an array of all configured locales (code, name, native name). ￼

⸻

Get Current Locale

{{ LaravelLocalization::getCurrentLocale() }}

Returns the active locale key. ￼

⸻

Language Selector Example

Add a language selector in your Blade view:

<ul>
@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
  <li>
    <a rel="alternate"
       hreflang="{{ $localeCode }}"
       href="{{ LaravelLocalization::getLocalizedURL($localeCode) }}">
       {{ $properties['native'] }}
    </a>
  </li>
@endforeach
</ul>

This lets users switch languages easily. ￼

⸻

Translated Routes

6. Define Translated Routes

To have different URLs per language, define translations in resources/lang/{locale}/routes.php:

// resources/lang/en/routes.php
return [
'about' => 'about',
];

// resources/lang/es/routes.php
return [
'about' => 'acerca',
];

Register them in routes:

Route::get(LaravelLocalization::transRoute('routes.about'), function () {
return view('about');
});

Localized routing now produces:
• /en/about
• /es/acerca ￼

⸻

Cache & Testing

7. Route Caching

By default, the package breaks Laravel’s default route cache. Use the provided commands:

php artisan route:trans:cache
php artisan route:trans:clear

List localized routes with:

php artisan route:trans:list en

(optional depending on version) ￼

⸻

Common Pitfalls
• Forgetting to wrap all localizable routes in the group
• Missing middleware for locale redirection
• Not using localized URLs in forms (may cause redirect loops) ￼

⸻

Summary

Feature Purpose
Localization package Adds multilingual routing & helpers
Localized routes Prefix routes by locale
Locale detection Detect via browser/session/cookie
Helpers Generate localized URLs, route names
Language selector Easy user language switch
Translated routes Different URL per locale
