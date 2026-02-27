<?php

namespace App\Filament\Resources\LanguageLines\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LanguageLineForm
{
    public static function configure(Schema $schema): Schema
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));

        $components = [
            TextInput::make('group')
                ->label(__('Group'))
                ->required()
                ->maxLength(255)
                ->default('*'),
            TextInput::make('key')
                ->label(__('Key'))
                ->required()
                ->maxLength(255),
        ];

        foreach ($locales as $locale) {
            $components[] = TextInput::make('text.'.$locale)
                ->label(__('Translation').' ('.$locale.')')
                ->maxLength(65535);
        }

        return $schema->components($components);
    }
}
