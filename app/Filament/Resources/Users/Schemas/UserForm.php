<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->helperText('Leave blank to keep the current password.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ]),
                Section::make('Roles & verification')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload(),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email verified at')
                            ->helperText('Set to mark the email as verified without sending a verification link.'),
                    ]),
            ]);
    }
}
