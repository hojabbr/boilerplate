<?php

namespace App\Filament\Resources\BlogPostSeriesResource\Pages;

use App\Core\Models\Language;
use App\Core\Services\Ai\Support\AiProviderOptions;
use App\Domains\Blog\Support\ImageStyleOptions;
use App\Filament\Resources\BlogPostSeriesResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditScheduledSeries extends EditRecord
{
    protected static string $resource = BlogPostSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $styles = $data['image_styles'] ?? null;
        if (($styles === null || $styles === []) && ! empty($data['image_style'] ?? null)) {
            $data['image_styles'] = [$data['image_style']];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $styles = $data['image_styles'] ?? [];
        if (is_array($styles) && $styles !== []) {
            $data['image_style'] = $styles[0];
        }

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        $daysOfWeekOptions = [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ];
        $hourOptions = [];
        for ($h = 0; $h < 24; $h++) {
            $hourOptions[$h] = sprintf('%02d:00', $h);
        }
        $languages = Language::query()->where('is_enabled', true)->orderBy('sort_order')->pluck('name', 'id')->all();
        $providers = AiProviderOptions::availableProviders();

        return $schema
            ->components([
                Section::make('Series details')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255),
                        Textarea::make('purpose')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('objective')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('topics')
                            ->rows(2)
                            ->columnSpanFull(),
                        DatePicker::make('start_date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Schedule')
                    ->schema([
                        CheckboxList::make('days_of_week')
                            ->label('Days of week')
                            ->options($daysOfWeekOptions)
                            ->required()
                            ->columns(7),
                        CheckboxList::make('run_at_hours')
                            ->label('Run at hours (UTC)')
                            ->options($hourOptions)
                            ->required()
                            ->columns(6),
                        TextInput::make('total_posts_limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('No limit'),
                    ]),
                Section::make('Generation options')
                    ->schema([
                        Select::make('provider')
                            ->options($providers)
                            ->required()
                            ->native(false),
                        Select::make('length')
                            ->options([
                                'short' => 'Short',
                                'medium' => 'Medium',
                                'long' => 'Long',
                            ])
                            ->required()
                            ->native(false),
                        Select::make('language_ids')
                            ->label('Languages')
                            ->options($languages)
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Toggle::make('generate_image')
                            ->live(),
                        CheckboxList::make('image_styles')
                            ->label('Image styles (one per post, in order)')
                            ->options(ImageStyleOptions::labels())
                            ->helperText('Styles rotate per generated post: 1st post uses 1st style, 2nd post uses 2nd, etc. Select at least one.')
                            ->minItems(1)
                            ->columns(2)
                            ->visible(fn ($get) => $get('generate_image') === true),
                        Toggle::make('publish_immediately'),
                        Toggle::make('is_active')
                            ->label('Series active (runs at scheduled times)'),
                    ])
                    ->columns(2),
            ]);
    }
}
