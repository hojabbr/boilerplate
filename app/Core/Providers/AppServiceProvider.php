<?php

namespace App\Core\Providers;

use App\Core\Contracts\PagePropsServiceInterface;
use App\Core\Features\BlogFeature;
use App\Core\Features\ContactFormFeature;
use App\Core\Features\FaqFeature;
use App\Core\Features\LoginFeature;
use App\Core\Features\PageFeature;
use App\Core\Features\RegistrationFeature;
use App\Core\Features\TestimonialFeature;
use App\Core\Inertia\TestingViewFinder;
use App\Core\Models\FeatureFlag;
use App\Core\Models\Language;
use App\Core\Models\Setting;
use App\Core\Observers\LanguageObserver;
use App\Core\Observers\SettingObserver;
use App\Core\Policies\FeatureFlagPolicy;
use App\Core\Policies\LanguageLinePolicy;
use App\Core\Policies\LanguagePolicy;
use App\Core\Services\PagePropsService as CorePagePropsService;
use App\Core\Services\SupportedLocalesService;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Policies\UserPolicy;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Models\BlogPostSeries;
use App\Domains\Blog\Policies\BlogPostPolicy;
use App\Domains\Blog\Policies\BlogPostSeriesPolicy;
use App\Domains\Contact\Models\ContactSubmission;
use App\Domains\Contact\Policies\ContactSubmissionPolicy;
use App\Domains\Faq\Models\Faq;
use App\Domains\Faq\Observers\FaqObserver;
use App\Domains\Faq\Policies\FaqPolicy;
use App\Domains\Landing\Models\LandingSection;
use App\Domains\Landing\Models\LandingSectionItem;
use App\Domains\Landing\Observers\LandingSectionItemObserver;
use App\Domains\Landing\Observers\LandingSectionObserver;
use App\Domains\Landing\Policies\LandingSectionPolicy;
use App\Domains\Page\Models\Page;
use App\Domains\Page\Observers\PageObserver;
use App\Domains\Page\Policies\PagePolicy;
use App\Domains\Testimonial\Models\Testimonial;
use App\Domains\Testimonial\Observers\TestimonialObserver;
use App\Domains\Testimonial\Policies\TestimonialPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Pennant\Feature;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\TranslationLoader\LanguageLine;

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
        Feature::define('faq', FaqFeature::class);
        Feature::define('testimonials', TestimonialFeature::class);
        Feature::define('login', LoginFeature::class);
        Feature::define('registration', RegistrationFeature::class);

        // Observers run for all CRUD (create/update/delete/restore/force delete) from Filament, API, tinker, etc.
        Language::observe(LanguageObserver::class);
        Setting::observe(SettingObserver::class);
        Page::observe(PageObserver::class);
        Faq::observe(FaqObserver::class);
        Testimonial::observe(TestimonialObserver::class);
        LandingSection::observe(LandingSectionObserver::class);
        LandingSectionItem::observe(LandingSectionItemObserver::class);

        // Clear the site settings cache when branding media is uploaded or removed.
        Media::created(function (Media $media): void {
            if ($media->model_type === Setting::class) {
                Cache::forget(Setting::siteCacheKey());
            }
        });
        Media::deleted(function (Media $media): void {
            if ($media->model_type === Setting::class) {
                Cache::forget(Setting::siteCacheKey());
            }
        });

        $this->registerPolicies();
        Gate::define('use-translation-manager', fn (?User $user) => $user !== null && $user->can('manage translations'));
        $this->injectSupportedLocalesFromDb();
        $this->configureDefaults();
    }

    /**
     * Register model policies (models moved to Domains/Core).
     */
    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(BlogPost::class, BlogPostPolicy::class);
        Gate::policy(BlogPostSeries::class, BlogPostSeriesPolicy::class);
        Gate::policy(ContactSubmission::class, ContactSubmissionPolicy::class);
        Gate::policy(Faq::class, FaqPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Testimonial::class, TestimonialPolicy::class);
        Gate::policy(LandingSection::class, LandingSectionPolicy::class);
        Gate::policy(FeatureFlag::class, FeatureFlagPolicy::class);
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(LanguageLine::class, LanguageLinePolicy::class);
    }

    /**
     * Inject DB-backed supported locales into config so mcamara and all config() callers use them.
     * Fallback to config when DB has no languages (e.g. before first seed).
     * Skips injection when DB or cache is unavailable (e.g. CI before migrations, PHPStan bootstrap).
     */
    protected function injectSupportedLocalesFromDb(): void
    {
        try {
            $locales = $this->app->make(SupportedLocalesService::class)->get();
        } catch (\Throwable) {
            return;
        }

        if ($locales === []) {
            return;
        }

        Config::set('laravellocalization.supportedLocales', $locales);
        try {
            $defaultQuery = Language::query()->where('is_default', true);
            if (Schema::hasColumn((new Language)->getTable(), 'is_enabled')) {
                $defaultQuery->where('is_enabled', true);
            }
            $defaultCode = $defaultQuery->value('code') ?? array_key_first($locales);
            Config::set('app.locale', $defaultCode);
        } catch (\Throwable) {
            Config::set('app.locale', array_key_first($locales));
        }
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
