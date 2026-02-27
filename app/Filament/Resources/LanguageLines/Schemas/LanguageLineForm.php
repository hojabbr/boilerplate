<?php

namespace App\Filament\Resources\LanguageLines\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LanguageLineForm
{
    public static function configure(Schema $schema): Schema
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));

        $translationComponents = [];

        foreach ($locales as $locale) {
            $translationComponents[] = TextInput::make('text.'.$locale)
                ->label(__('Translation').' ('.$locale.')')
                ->maxLength(65535);
        }

        return $schema->components([
            Section::make('Key')
                ->schema([
                    TextInput::make('group')
                        ->label(__('Group'))
                        ->helperText('Use * for values stored in the database, or a PHP filename (e.g. auth, validation).')
                        ->required()
                        ->maxLength(255)
                        ->default('*'),
                    TextInput::make('key')
                        ->label(__('Key'))
                        ->helperText('Dot-notation key used to look up this translation (e.g. messages.welcome).')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),
            Section::make('Translations')
                ->schema($translationComponents)
                ->columns(2),
        ]);
    }
}
