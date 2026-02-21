<?php

use App\Domains\Blog\Http\Controllers\BlogController;
use App\Domains\Contact\Http\Controllers\ContactController;
use App\Domains\Dashboard\Http\Controllers\DashboardController;
use App\Domains\Landing\Http\Controllers\LandingController;
use App\Domains\Page\Http\Controllers\PageController;
use App\Domains\Search\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Root path: ensure web middleware (and thus LocaleCookieRedirect) runs so
// a visit to / redirects to the user's chosen locale from cookie/session.
Route::get('/', function () {
    return redirect()->to(LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale()));
})->middleware('web');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeCookieRedirect', 'localizationRedirect', 'localeViewPath', 'setLocalizedFortifyRedirects'],
    ], function () {
        require base_path('vendor/laravel/fortify/routes/routes.php');

        Route::get('/', LandingController::class)->name('home');

        Route::get('dashboard', DashboardController::class)
            ->middleware(['auth', 'verified'])
            ->name('dashboard');

        Route::get('search', SearchController::class)->name('search');
        Route::get('page/{slug}', [PageController::class, 'show'])->name('page.show');
        Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('contact', [ContactController::class, 'show'])->name('contact.show');
        Route::post('contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

        require __DIR__.'/settings.php';
    });
