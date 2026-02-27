<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Core\Models\Language;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('URL-safe identifier. Only letters, numbers, hyphens, and underscores.')
                            ->rules(['alpha_dash']),
                        Textarea::make('excerpt')
                            ->label('Excerpt (plain text, one or two sentences)')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('Settings')
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
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        DateTimePicker::make('published_at')
                            ->label('Published at')
                            ->helperText('Leave empty to save as a draft.'),
                        TextInput::make('meta_description')
                            ->label('Meta description')
                            ->helperText('Shown in search engine results. Aim for ~155 characters.')
                            ->maxLength(255),
                    ]),
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Gallery images')
                            ->image()
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->conversion('thumb'),
                        SpatieMediaLibraryFileUpload::make('videos')
                            ->label('Videos')
                            ->collection('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->multiple(),
                        SpatieMediaLibraryFileUpload::make('documents')
                            ->label('Documents')
                            ->collection('documents')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->multiple(),
                    ])
                    ->collapsible(),
            ]);
    }
}
