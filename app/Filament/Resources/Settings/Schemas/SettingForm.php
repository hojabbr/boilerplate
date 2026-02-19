<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->disabledOn('edit'),
                TextInput::make('company_name')
                    ->maxLength(255),
                TextInput::make('tagline')
                    ->maxLength(255),
                TextInput::make('address')
                    ->maxLength(65535),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                KeyValue::make('social_links'),
            ]);
    }
}
