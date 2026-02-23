<?php

namespace App\Filament\Resources\Languages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('script')
                    ->maxLength(255),
                TextInput::make('regional')
                    ->maxLength(255),
                Select::make('direction')
                    ->options(['ltr' => 'LTR', 'rtl' => 'RTL'])
                    ->default('ltr')
                    ->required(),
                Toggle::make('is_default'),
                Toggle::make('is_enabled')
                    ->helperText('When disabled, this language is hidden from the site, language switcher, and all locale-dependent features.'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
