<?php

use App\Models\LandingSection;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Pennant\Feature;

test('setting cache is invalidated on update and next request returns fresh data', function () {
    $setting = Setting::site();
    $originalName = $setting->company_name ?? 'Original';
    $setting->update(['company_name' => ['en' => $originalName]]);

    $response1 = $this->get('/en');
    $response1->assertOk();
    $response1->assertInertia(fn (Assert $page) => $page
        ->has('settings')
        ->where('settings.company_name', $originalName)
    );

    $setting->update(['company_name' => ['en' => 'Updated Company Name']]);

    $response2 = $this->get('/en');
    $response2->assertOk();
    $response2->assertInertia(fn (Assert $page) => $page
        ->has('settings')
        ->where('settings.company_name', 'Updated Company Name')
    );
});

test('page cache is invalidated on update and next request returns fresh data', function () {
    Feature::activate('pages');

    $page = Page::create([
        'slug' => 'cache-test-page',
        'type' => 'custom',
        'title' => ['en' => 'Original Title'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    $response1 = $this->get('/en/page/cache-test-page');
    $response1->assertOk();
    $response1->assertInertia(fn (Assert $page) => $page
        ->where('page.title', 'Original Title')
    );

    $page->update(['title' => ['en' => 'Updated Title']]);

    $response2 = $this->get('/en/page/cache-test-page');
    $response2->assertOk();
    $response2->assertInertia(fn (Assert $page) => $page
        ->where('page.title', 'Updated Title')
    );
});

test('landing sections cache is invalidated when section is updated and next request returns fresh data', function () {
    $section = LandingSection::create([
        'type' => 'features',
        'sort_order' => 0,
        'title' => ['en' => 'Original Section Title'],
    ]);

    $response1 = $this->get('/en');
    $response1->assertOk();
    $response1->assertInertia(fn (Assert $page) => $page
        ->has('sections')
        ->where('sections.0.title', 'Original Section Title')
    );

    $section->update(['title' => ['en' => 'Updated Section Title']]);

    $response2 = $this->get('/en');
    $response2->assertOk();
    $response2->assertInertia(fn (Assert $page) => $page
        ->has('sections')
        ->where('sections.0.title', 'Updated Section Title')
    );
});

test('setting cache is used on read and invalidated on delete', function () {
    $key = 'setting.site';
    Cache::forget($key);

    $this->get('/en');
    $this->assertNotNull(Cache::get($key));

    $setting = Setting::where('key', 'site')->first();
    $setting->delete();
    $this->assertNull(Cache::get($key));
});

test('page cache is invalidated on delete', function () {
    Feature::activate('pages');

    $page = Page::create([
        'slug' => 'cache-delete-test',
        'type' => 'custom',
        'title' => ['en' => 'To Delete'],
        'body' => ['en' => '<p>Body</p>'],
    ]);

    $this->get('/en/page/cache-delete-test');
    $this->assertNotNull(Cache::get('page.cache-delete-test'));

    $page->delete();
    $this->assertNull(Cache::get('page.cache-delete-test'));
});
