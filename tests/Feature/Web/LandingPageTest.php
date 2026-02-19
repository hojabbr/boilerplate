<?php

use Inertia\Testing\AssertableInertia as Assert;

test('landing page returns 200 and Inertia props with locale and settings', function () {
    $response = $this->get('/en');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('welcome')
        ->has('locale')
        ->where('locale', 'en')
        ->has('settings')
        ->has('features')
    );
});
