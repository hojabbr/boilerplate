<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Filament\Support\CommonColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('key')
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                ...CommonColumns::timestampColumns(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
