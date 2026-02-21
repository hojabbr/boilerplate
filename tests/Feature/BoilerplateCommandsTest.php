<?php

use Illuminate\Support\Facades\Artisan;

test('boilerplate:locale fails when run with no interaction (missing required input)', function () {
    $exitCode = Artisan::call('boilerplate:locale', ['--no-interaction' => true]);

    expect($exitCode)->toBe(1);
    expect(Artisan::output())->toContain('Locale code');
});

test('boilerplate:domain fails when run with no interaction (missing required input)', function () {
    $exitCode = Artisan::call('boilerplate:domain', ['--no-interaction' => true]);

    expect($exitCode)->toBe(1);
    expect(Artisan::output())->toContain('Domain name');
});

test('boilerplate:domain --rollback with invalid name fails', function () {
    $exitCode = Artisan::call('boilerplate:domain', ['--rollback' => 'invalid-name']);

    expect($exitCode)->toBe(1);
    expect(Artisan::output())->toContain('PascalCase');
});

test('boilerplate:domain --rollback with non-existent domain succeeds and reports nothing to roll back', function () {
    $exitCode = Artisan::call('boilerplate:domain', ['--rollback' => 'NonExistentDomain']);

    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('No scaffolded artifacts');
});

test('boilerplate:locale --rollback with non-existent locale succeeds', function () {
    $exitCode = Artisan::call('boilerplate:locale', ['--rollback' => 'xx']);

    expect($exitCode)->toBe(0);
});
