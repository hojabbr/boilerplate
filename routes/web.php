<?php

use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\SearchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLocalizedFortifyRedirects'],
    ], function () {
        require base_path('vendor/laravel/fortify/routes/routes.php');

        Route::get('/', LandingController::class)->name('home');

        Route::get('dashboard', function () {
            return Inertia::render('dashboard', [
                'messages' => [
                    'title' => __('common.dashboard'),
                ],
            ]);
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::get('search', SearchController::class)->name('search');
        Route::get('page/{slug}', [PageController::class, 'show'])->name('page.show');
        Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('contact', [ContactController::class, 'show'])->name('contact.show');
        Route::post('contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

        require __DIR__.'/settings.php';
    });
