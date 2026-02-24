<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Core\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('language_id')
                            ->relationship(
                                'language',
                                'name',
                                fn (Builder $query) => $query->orderBy('sort_order')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Language $record): string => $record->is_enabled
                                ? $record->name
                                : $record->name.' (inactive)')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        TextInput::make('question')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
