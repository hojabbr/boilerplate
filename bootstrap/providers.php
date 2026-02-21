<?php

return [
    App\Core\Providers\AppServiceProvider::class,
    Spatie\TranslationLoader\TranslationServiceProvider::class,
    App\Core\Providers\Filament\AdminPanelProvider::class,
    App\Core\Providers\FortifyServiceProvider::class,
    App\Core\Providers\HorizonServiceProvider::class,
    App\Core\Providers\TelescopeServiceProvider::class,
];
