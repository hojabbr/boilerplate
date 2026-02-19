---
name: spatie-laravel-query-builder
description: "Builds dynamic Eloquent queries from HTTP API requests by allowing filtering, sorting, including relations, and selecting specific fields. Activates when building API endpoints that accept query parameters such as filter, sort, include, or fields, or when the user mentions query builder, API filtering, sorting, JSON API, or allowedFilters/allowedSorts."
license: MIT
metadata:
  author: Spatie
---

# Spatie Laravel Query Builder (v6)

## When to Apply

Activate this skill when:

- Building API endpoints with dynamic query parameters
- Allowing clients to filter results via `?filter[...]`
- Accepting inclusion of relations via `?include=...`
- Supporting sorting via `?sort=...`
- Selecting specific fields via `?fields[...]`
- Extending queries cleanly while avoiding manual request parsing

## Documentation

Use `search-docs` to open the **Spatie Laravel Query Builder v6** docs.  
This package lets you build Eloquent queries from API request parameters that follow a convention similar to the JSON API specification.  [oai_citation:1‡Spatie](https://spatie.be/docs/laravel-query-builder/v6/introduction?utm_source=chatgpt.com)

## Installation

Install the package via Composer:

```bash
composer require spatie/laravel-query-builder
```

Laravel auto‑registers the service provider. Optionally publish the config:

php artisan vendor:publish \
  --provider="Spatie\QueryBuilder\QueryBuilderServiceProvider" \
  --tag="query-builder-config"

The published config allows customization of parameter names and exceptions.  ￼

⸻

Basic Usage

Filtering

Filter Eloquent results based on request:

use Spatie\QueryBuilder\QueryBuilder;

$users = QueryBuilder::for(User::class)
    ->allowedFilters('name')
    ->get();

This processes requests like:

GET /users?filter[name]=John

Only explicit allowed filters are applied; others throw an exception unless configured otherwise.  ￼

⸻

Including Relationships

Include related Eloquent models based on request:

$users = QueryBuilder::for(User::class)
    ->allowedIncludes('posts')
    ->get();

Processes:

GET /users?include=posts

Allows eager loading based on request.  ￼

⸻

Sorting

Sort results using query string:

$users = QueryBuilder::for(User::class)
    ->allowedSorts('id', 'name')
    ->get();

Processes requests like:

GET /users?sort=name

Use -field for descending order.  ￼

⸻

Selecting Fields

Limit which fields are selected in results:

$users = QueryBuilder::for(User::class)
    ->allowedFields(['id', 'email'])
    ->get();

Supports:

GET /users?fields[users]=id,email

This returns only specified attributes on the models.  ￼

⸻

Advanced Features

Existing Queries

Wrap an existing query:

$query = User::where('active', true);

$users = QueryBuilder::for($query)
    ->allowedIncludes('posts')
    ->get();

You can chain standard Eloquent methods.  ￼

⸻

Pagination

Use Laravel’s built‑in pagination since Query Builder extends Eloquent:

->paginate();

It respects requested filters and sorts.  ￼

⸻

Common Filters
	•	Partial filters (default for string filters)
	•	Exact filters using AllowedFilter::exact()
	•	Scope filters corresponding to local model scopes
	•	Custom filters using AllowedFilter::custom()
	•	Trashed filters for soft deletes
	•	Ignored values stop filters from applying when matching certain values
	•	Default filter values for missing parameters  ￼

⸻

Error Handling

If a request contains a filter, sort, or include that isn’t explicitly allowed, the package throws a validation exception. You can disable this behavior in the config if needed.  ￼

⸻

Tips & Best Practices
	•	Always whitelist fields, filters, sorts, and includes to prevent performance or security issues.
	•	Use local Eloquent scopes with scope filters for reusable logic.
	•	Use custom filters for complex criteria (e.g., JSON columns or relationship properties).  ￼

⸻

Summary

Feature	Purpose
Filtering	Restrict results based on query parameters
Sorting	Order results via sort parameter
Includes	Eager load relationships via include
Fields	Limit selected fields via fields
Extends Eloquent	Integrates with existing query builders
Customization	Define custom filters, aliases, and scopes

---