<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLocalizedFortifyRedirects'],
    ], function () {
        require base_path('vendor/laravel/fortify/routes/routes.php');

        Route::get('/', function () {
            return Inertia::render('welcome', [
                'canRegister' => Features::enabled(Features::registration()),
            ]);
        })->name('home');

        Route::get('dashboard', function () {
            return Inertia::render('dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        require __DIR__.'/settings.php';
    });
