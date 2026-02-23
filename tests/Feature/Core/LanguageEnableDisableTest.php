<?php

use App\Core\Models\Language;
use App\Core\Services\SupportedLocalesService;
use Illuminate\Validation\ValidationException;

test('SupportedLocalesService returns only enabled languages', function () {
    Language::factory()->create(['code' => 'en', 'name' => 'English', 'sort_order' => 0, 'is_enabled' => true]);
    Language::factory()->create(['code' => 'de', 'name' => 'German', 'sort_order' => 1, 'is_enabled' => true]);
    Language::factory()->create(['code' => 'fa', 'name' => 'Persian', 'sort_order' => 2, 'is_enabled' => false]);

    app(SupportedLocalesService::class)->clearCache();
    $locales = app(SupportedLocalesService::class)->get();

    expect($locales)->toHaveKeys(['en', 'de']);
    expect($locales)->not->toHaveKey('fa');
});

test('cannot disable the last enabled language', function () {
    $en = Language::factory()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);

    $en->is_enabled = false;
    $en->save();
})->throws(ValidationException::class);

test('disabling the default language assigns default to another enabled language', function () {
    Language::factory()->create(['code' => 'en', 'name' => 'English', 'sort_order' => 0, 'is_default' => true, 'is_enabled' => true]);
    $de = Language::factory()->create(['code' => 'de', 'name' => 'German', 'sort_order' => 1, 'is_default' => false, 'is_enabled' => true]);

    $en = Language::query()->where('code', 'en')->first();
    $en->is_enabled = false;
    $en->save();

    expect(Language::query()->where('code', 'en')->first()->is_default)->toBeFalse();
    expect(Language::query()->where('code', 'de')->first()->is_default)->toBeTrue();
});
