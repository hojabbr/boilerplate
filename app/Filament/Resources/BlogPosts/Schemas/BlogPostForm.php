<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('language_id')
                    ->relationship('language', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->rules(['alpha_dash']),
                TextInput::make('title')
                    ->maxLength(255),
                RichEditor::make('excerpt')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'bulletList',
                        'orderedList',
                    ])
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
                TextInput::make('meta_description')
                    ->maxLength(255),
                DateTimePicker::make('published_at'),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('gallery')
                            ->label('Gallery images')
                            ->image()
                            ->multiple()
                            ->directory('blog-gallery')
                            ->reorderable()
                            ->dehydrated(false),
                        FileUpload::make('videos')
                            ->label('Videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->multiple()
                            ->directory('blog-videos')
                            ->dehydrated(false),
                        FileUpload::make('documents')
                            ->label('Documents')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->multiple()
                            ->directory('blog-documents')
                            ->dehydrated(false),
                    ])
                    ->collapsible(),
            ]);
    }
}
