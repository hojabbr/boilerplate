<?php

namespace App\Core\Providers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Inertia\TestingViewFinder;
use App\Core\Models\Language;
use App\Core\Models\Setting;
use App\Core\Observers\LanguageObserver;
use App\Core\Observers\SettingObserver;
use App\Core\Services\PagePropsService as CorePagePropsService;
use App\Domains\Landing\Models\LandingSection;
use App\Domains\Landing\Models\LandingSectionItem;
use App\Domains\Landing\Observers\LandingSectionItemObserver;
use App\Domains\Landing\Observers\LandingSectionObserver;
use App\Domains\Page\Models\Page;
use App\Domains\Page\Observers\PageObserver;
use App\Features\BlogFeature;
use App\Features\ContactFormFeature;
use App\Features\LoginFeature;
use App\Features\PageFeature;
use App\Features\RegistrationFeature;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        $this->app->bind(PagePropsServiceInterface::class, CorePagePropsService::class);

        $this->app->bind('inertia.testing.view-finder', function ($app) {
            return new TestingViewFinder(
                $app['files'],
                $app['config']->get('inertia.testing.page_extensions', ['tsx', 'ts', 'jsx', 'js']),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Feature::define('blog', BlogFeature::class);
        Feature::define('page', PageFeature::class);
        Feature::define('contact-form', ContactFormFeature::class);
        Feature::define('login', LoginFeature::class);
        Feature::define('registration', RegistrationFeature::class);

        // Observers run for all CRUD (create/update/delete/restore/force delete) from Filament, API, tinker, etc.
        Language::observe(LanguageObserver::class);
        Setting::observe(SettingObserver::class);
        Page::observe(PageObserver::class);
        LandingSection::observe(LandingSectionObserver::class);
        LandingSectionItem::observe(LandingSectionItemObserver::class);
        $this->registerPolicies();
        $this->configureDefaults();
    }

    /**
     * Register model policies (models moved to Domains/Core).
     */
    protected function registerPolicies(): void
    {
        Gate::policy(\App\Domains\Auth\Models\User::class, \App\Domains\Auth\Policies\UserPolicy::class);
        Gate::policy(\App\Domains\Blog\Models\BlogPost::class, \App\Domains\Blog\Policies\BlogPostPolicy::class);
        Gate::policy(\App\Domains\Contact\Models\ContactSubmission::class, \App\Domains\Contact\Policies\ContactSubmissionPolicy::class);
        Gate::policy(\App\Domains\Page\Models\Page::class, \App\Domains\Page\Policies\PagePolicy::class);
        Gate::policy(\App\Domains\Landing\Models\LandingSection::class, \App\Domains\Landing\Policies\LandingSectionPolicy::class);
        Gate::policy(\App\Core\Models\FeatureFlag::class, \App\Core\Policies\FeatureFlagPolicy::class);
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
