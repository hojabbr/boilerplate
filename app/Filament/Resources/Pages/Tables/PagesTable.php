<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Filament\Support\CommonColumns;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                IconColumn::make('show_in_navigation')
                    ->boolean()
                    ->label('Nav')
                    ->sortable(),
                IconColumn::make('show_in_footer')
                    ->boolean()
                    ->label('Footer')
                    ->sortable(),
                TextColumn::make('order')
                    ->sortable(),
                CommonColumns::deletedAtColumn(),
                ...CommonColumns::timestampColumns(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ])
                    ->native(false),
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
