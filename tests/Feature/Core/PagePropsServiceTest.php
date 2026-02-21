<?php

use App\Core\Models\Setting;
use App\Core\Services\PagePropsService;
use Laravel\Pennant\Feature;

test('page props service settings slice returns company name and tagline from setting or config', function () {
    $setting = Setting::site();
    $service = app(PagePropsService::class);

    $slice = $service->settingsSlice($setting);

    expect($slice)->toHaveKeys(['company_name', 'tagline', 'email', 'phone', 'social_links']);
    expect($slice['company_name'])->toBeString();
    expect($slice['tagline'])->toBeString();
    expect($slice['social_links'])->toBeArray();
});

test('page props service features array returns boolean flags for page blog contactForm', function () {
    Feature::activate('blog');
    Feature::deactivate('page');
    Feature::deactivate('contact-form');

    $service = app(PagePropsService::class);
    $features = $service->featuresArray();

    expect($features)->toHaveKeys(['page', 'blog', 'contactForm']);
    expect($features['blog'])->toBeTrue();
    expect($features['page'])->toBeFalse();
    expect($features['contactForm'])->toBeFalse();
});
