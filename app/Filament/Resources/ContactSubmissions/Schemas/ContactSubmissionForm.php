<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->disabled(),
                TextInput::make('subject')
                    ->disabled(),
                Textarea::make('message')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('locale')
                    ->disabled(),
            ]);
    }
}
