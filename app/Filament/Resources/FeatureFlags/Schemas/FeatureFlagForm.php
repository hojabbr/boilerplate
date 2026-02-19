<?php

namespace App\Filament\Resources\FeatureFlags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FeatureFlagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->disabled(),
                TextInput::make('label')
                    ->disabled(),
                Toggle::make('is_active')
                    ->label('Active'),
            ]);
    }
}
