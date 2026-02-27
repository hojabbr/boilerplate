<?php

namespace App\Filament\Resources\FeatureFlags\Tables;

use App\Filament\Support\CommonColumns;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeatureFlagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('label')
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                ...CommonColumns::timestampColumns(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
