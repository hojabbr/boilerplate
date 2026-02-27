<?php

namespace App\Filament\Resources\BlogPostSeriesResource\Tables;

use App\Domains\Blog\Models\BlogPostSeries;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogPostSeriesTable
{
    public static function configure(Table $table): Table
    {
        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_active')
                    ->label('')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('name')
                    ->label('Name')
                    ->placeholder('—')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(35)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('days_of_week')
                    ->label('Days')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($d) => $dayNames[$d] ?? (string) $d, is_array($state) ? $state : ($state !== null ? [$state] : []))))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('run_at_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($h) => sprintf('%02d:00', (int) $h), is_array($state) ? $state : ($state !== null ? [$state] : []))))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('posts_generated')
                    ->label('Posts')
                    ->suffix(fn ($record) => $record->total_posts_limit ? " / {$record->total_posts_limit}" : '')
                    ->sortable(),
                TextColumn::make('last_run_at')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->default('1'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('gray')
                    ->visible(fn (BlogPostSeries $record) => $record->is_active)
                    ->action(fn (BlogPostSeries $record) => $record->update(['is_active' => false]))
                    ->modalHeading('Deactivate scheduled series')
                    ->modalDescription('This will stop the series from running. You can reactivate it from the Edit page.')
                    ->successNotificationTitle('Series deactivated')
                    ->requiresConfirmation(),
            ]);
    }
}
