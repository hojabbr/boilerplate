<?php

use Inertia\Testing\AssertableInertia as Assert;

test('localized home route returns locale in shared props', function () {
    refreshApplicationWithLocale('en');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale')
        ->where('locale', 'en')
        ->has('supportedLocales')
    );
});

test('localized dashboard route returns locale in shared props', function () {
    refreshApplicationWithLocale('en');

    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('locale')
        ->where('locale', 'en')
    );
});
