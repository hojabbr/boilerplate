<?php

namespace App\Filament\Support;

use Filament\Tables\Columns\TextColumn;

class CommonColumns
{
    /**
     * Standard created_at and updated_at columns (toggleable, hidden by default).
     *
     * @return array<int, TextColumn>
     */
    public static function timestampColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * Standard deleted_at column for soft-deletable tables (toggleable, hidden by default).
     */
    public static function deletedAtColumn(): TextColumn
    {
        return TextColumn::make('deleted_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}
