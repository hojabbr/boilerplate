<?php

use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Pennant\Feature;

test('login route returns 404 when login feature is inactive', function () {
    Feature::for(null)->deactivate('login');

    $response = $this->get(route('login'));

    $response->assertNotFound();
});

test('login store route returns 404 when login feature is inactive', function () {
    Feature::for(null)->deactivate('login');

    $response = $this->post(route('login.store'), [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response->assertNotFound();
});

test('login route returns 200 when login feature is inactive but intended url is admin panel', function () {
    Feature::for(null)->deactivate('login');

    $response = $this->withSession(['url.intended' => 'http://localhost/admin'])
        ->get(route('login'));

    $response->assertOk();
});

test('login store route is allowed when login feature is inactive but intended url is admin panel', function () {
    Feature::for(null)->deactivate('login');
    $user = \App\Domains\Auth\Models\User::factory()->create();

    $response = $this->withSession(['url.intended' => 'http://localhost/admin'])
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $response->assertRedirect();
    $this->assertAuthenticated();
});

test('login route returns 200 when login feature is active', function () {
    Feature::for(null)->activate('login');

    $response = $this->get(route('login'));

    $response->assertOk();
});

test('register route returns 404 when registration feature is inactive', function () {
    Feature::for(null)->deactivate('registration');

    $response = $this->get(route('register'));

    $response->assertNotFound();
});

test('register store route returns 404 when registration feature is inactive', function () {
    Feature::for(null)->deactivate('registration');

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();
});

test('register route returns 200 when registration feature is active', function () {
    Feature::for(null)->activate('registration');

    $response = $this->get(route('register'));

    $response->assertOk();
});

test('shared inertia props include features login and registration', function () {
    Feature::for(null)->activate('login');
    Feature::for(null)->activate('registration');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('features')
        ->where('features.login', true)
        ->where('features.registration', true)
    );
});

test('shared inertia props reflect deactivated login and registration', function () {
    Feature::for(null)->deactivate('login');
    Feature::for(null)->deactivate('registration');

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('features')
        ->where('features.login', false)
        ->where('features.registration', false)
    );
});
