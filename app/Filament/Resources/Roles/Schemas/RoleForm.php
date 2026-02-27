<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role name')
                            ->placeholder('e.g. Admin, Editor, Moderator')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('guard_name')
                            ->label('Guard name')
                            ->helperText('Usually "web" for browser-based authentication.')
                            ->default('web')
                            ->required()
                            ->maxLength(255),
                        Select::make('permissions')
                            ->label('Permissions')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
