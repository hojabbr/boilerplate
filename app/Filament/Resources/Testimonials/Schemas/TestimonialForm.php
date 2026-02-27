<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Core\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Testimonial')
                    ->schema([
                        Select::make('language_id')
                            ->label('Language')
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
                            ->helperText('Lower numbers appear first.')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Textarea::make('quote')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('author')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('role')
                            ->maxLength(255)
                            ->placeholder('e.g. CTO, TechStart'),
                    ])
                    ->columns(2),
            ]);
    }
}
