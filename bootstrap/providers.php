<?php

use App\Core\Providers\AppServiceProvider;
use App\Core\Providers\Filament\AdminPanelProvider;
use App\Core\Providers\FortifyServiceProvider;
use App\Core\Providers\HorizonServiceProvider;
use App\Core\Providers\TelescopeServiceProvider;
use Spatie\TranslationLoader\TranslationServiceProvider;

return [
    AppServiceProvider::class,
    TranslationServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
    TelescopeServiceProvider::class,
];
