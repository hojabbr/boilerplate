<?php

namespace App\Filament\Resources\LandingSections\Tables;

use App\Filament\Support\CommonColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LandingSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                CommonColumns::deletedAtColumn(),
                ...CommonColumns::timestampColumns(),
            ])
            ->filters([
                TrashedFilter::make(),
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
