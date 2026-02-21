<?php

namespace App\Filament\Resources\FeatureFlags\Pages;

use App\Filament\Resources\FeatureFlags\FeatureFlagResource;
use Filament\Resources\Pages\EditRecord;
use Laravel\Pennant\Feature;

class EditFeatureFlag extends EditRecord
{
    protected static string $resource = FeatureFlagResource::class;

    protected function afterSave(): void
    {
        // Ensure Pennant (null scope) is synced so the UI and routes respect the toggle immediately.
        $flag = $this->record;
        if ($flag->is_active) {
            Feature::for(null)->activate($flag->key);
        } else {
            Feature::for(null)->deactivate($flag->key);
        }
    }
}
