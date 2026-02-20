<?php

/**
 * Run Laravel IDE Helper commands only when the dev dependency is installed.
 * Allows "composer install --no-dev" (e.g. in CI release) to succeed without the package.
 */
$vendorDir = __DIR__.'/../vendor/barryvdh/laravel-ide-helper';
if (! is_dir($vendorDir)) {
    return;
}

$commands = [
    'php artisan ide-helper:generate --ansi',
    'php artisan ide-helper:models -M --ansi',
    'php artisan ide-helper:meta --ansi',
];

$baseDir = dirname(__DIR__);
foreach ($commands as $command) {
    passthru(sprintf('cd %s && %s', escapeshellarg($baseDir), $command));
}
