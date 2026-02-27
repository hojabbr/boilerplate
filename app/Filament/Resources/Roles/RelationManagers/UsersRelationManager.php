<?php

namespace App\Filament\Resources\Roles\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Users with this role';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->label('Edit user')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
