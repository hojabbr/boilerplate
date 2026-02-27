<?php

namespace App\Filament\Resources\Languages\Tables;

use App\Filament\Support\CommonColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                CommonColumns::deletedAtColumn(),
                ...CommonColumns::timestampColumns(),
            ])
            ->filters([
                TrashedFilter::make(),
                TernaryFilter::make('is_enabled')
                    ->label('Enabled')
                    ->placeholder('All')
                    ->trueLabel('Enabled only')
                    ->falseLabel('Disabled only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
