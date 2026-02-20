<?php

use App\Domains\Auth\Models\User;
use App\Domains\Landing\Models\LandingSection;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->guard = 'web';
    Permission::firstOrCreate(['name' => 'manage landing sections', 'guard_name' => $this->guard]);
});

test('user with manage landing sections permission can view any landing sections', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage landing sections');

    expect($user->can('viewAny', LandingSection::class))->toBeTrue();
});

test('user with manage landing sections permission can update a landing section', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage landing sections');
    $section = LandingSection::create(['type' => 'hero', 'sort_order' => 0]);

    expect($user->can('update', $section))->toBeTrue();
});

test('user without manage landing sections permission cannot view any landing sections', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', LandingSection::class))->toBeFalse();
});

test('user without manage landing sections permission cannot update a landing section', function () {
    $user = User::factory()->create();
    $section = LandingSection::create(['type' => 'hero', 'sort_order' => 0]);

    expect($user->can('update', $section))->toBeFalse();
});
