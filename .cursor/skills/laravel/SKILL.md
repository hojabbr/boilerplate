---
name: laravel-framework
description: "Covers the core Laravel framework for building web applications in PHP 8.x. Activates when setting up a Laravel project, defining routes, creating controllers and models, building APIs or full-stack applications, using Eloquent ORM, configuring views with Blade, or when the user mentions MVC, Artisan, middleware, requests, responses, migrations, authentication, testing, queues, or caching."
license: MIT
metadata:
  author: Laravel
---

# Laravel Framework (Laravel 12 + PHP 8.x)

## When to Apply

Activate this skill when:

- Initialising a new Laravel project
- Defining application **routes**
- Creating **controllers** and **models**
- Building APIs or full-stack applications
- Using Eloquent ORM for database interaction
- Compiling assets or templating with Blade
- Configuring queues, caching, events, or jobs
- Writing tests or automated workflows

## Documentation

Use `search-docs` for detailed Laravel concepts like routing, middleware, database management, testing, and more.  [oai_citation:1‡Laravel](https://laravel.com/docs/12.x/documentation?utm_source=chatgpt.com)

---

## What is Laravel?

Laravel is a free, open-source **PHP web application framework** for building modern web applications. It follows the **Model-View-Controller (MVC)** pattern and provides expressive, elegant syntax and powerful developer tools.  [oai_citation:2‡Wikipedia](https://en.wikipedia.org/wiki/Laravel?utm_source=chatgpt.com)

---

## Getting Started

### Installation

Install Laravel globally via Composer:

```bash
composer global require laravel/installer
```

Create a new Laravel project:

```bash
laravel new my-app
```

Or via Composer directly:

composer create-project laravel/laravel my-app

Serving Your App

Start the development server:

php artisan serve

Your application will be available at http://localhost:8000.  ￼

⸻

Core Concepts

Routing

Define application routes in routes/web.php or routes/api.php:

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Routes map URLs to controllers or closures.  ￼

⸻

Controllers

Generate a controller using Artisan:

php artisan make:controller UserController

Controllers handle request logic and return responses.

⸻

Views & Blade

Laravel’s templating engine is Blade:

<h1>Hello, {{ $name }}</h1>

Blade supports layouts, sections, and includes.  ￼

⸻

Database & Eloquent ORM

Migrations

Create database tables with migrations:

php artisan make:migration create_users_table

Run migrations:

php artisan migrate


⸻

Models & Eloquent

Models represent database tables and relationships:

class User extends Model {}

Eloquent provides a clean ActiveRecord implementation for querying the database.  ￼

⸻

Middleware

Middleware filters incoming requests:

php artisan make:middleware CheckAge

Then register in app/Http/Kernel.php.  ￼

⸻

Artisan Console

Laravel includes the Artisan CLI to automate tasks:
	•	php artisan migrate
	•	php artisan route:list
	•	php artisan make:model Post

Artisan helps boost productivity.  ￼

⸻

Authentication & Security

Laravel provides built-in authentication scaffolding and guard configuration. It also protects against:
	•	CSRF (Cross-Site Request Forgery)
	•	XSS (Cross-Site Scripting)
	•	SQL Injection

Security features are enabled by default.  ￼

⸻

Testing

Laravel integrates with PHPUnit and provides expressive test helpers:

php artisan test

You can write feature and unit tests.  ￼

⸻

Queues, Jobs & Events

Laravel supports background processing with queues and jobs:

php artisan queue:work

Events allow decoupled communication between components.  ￼

⸻

Caching & Sessions

Laravel makes caching easy with drivers like Redis, Memcached, or file cache. Session management stores user data across requests.  ￼

⸻

Deployment

Key deployment steps include:
	•	Configuring .env
	•	Caching routes/config: php artisan optimize
	•	Setting up queue workers
	•	Serving app with a suitable web server (Nginx, Apache)

⸻

Summary

Concept	Purpose
MVC Architecture	Organised code structure separating concerns
Routing	Defines how requests are handled
Eloquent ORM	Simplifies database interaction
Blade	Templating for views
Artisan	CLI tooling for tasks
Middleware	Request filtering
Testing	Ensures code correctness
Queues & Jobs	Background processing
Caching	Improves performance

---