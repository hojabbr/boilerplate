<?php

namespace App\Providers;

use App\Features\BlogFeature;
use App\Features\ContactFormFeature;
use App\Features\PagesFeature;
use App\Models\LandingSection;
use App\Models\LandingSectionItem;
use App\Models\Language;
use App\Models\Page;
use App\Models\Setting;
use App\Observers\LandingSectionItemObserver;
use App\Observers\LandingSectionObserver;
use App\Observers\LanguageObserver;
use App\Observers\PageObserver;
use App\Observers\SettingObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Pennant\Feature;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Feature::define('blog', BlogFeature::class);
        Feature::define('pages', PagesFeature::class);
        Feature::define('contact-form', ContactFormFeature::class);

        // Observers run for all CRUD (create/update/delete/restore/force delete) from Filament, API, tinker, etc.
        Language::observe(LanguageObserver::class);
        Setting::observe(SettingObserver::class);
        Page::observe(PageObserver::class);
        LandingSection::observe(LandingSectionObserver::class);
        LandingSectionItem::observe(LandingSectionItemObserver::class);
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
