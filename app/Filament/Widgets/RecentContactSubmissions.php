<?php

namespace App\Filament\Widgets;

use App\Domains\Contact\Models\ContactSubmission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentContactSubmissions extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => ContactSubmission::query()->latest()->limit(10)
            )
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('subject'),
                TextColumn::make('created_at')->dateTime(),
            ]);
    }
}
