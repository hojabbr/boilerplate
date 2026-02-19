<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->rules(['alpha_dash']),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('body')
                    ->required()
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
                Select::make('type')
                    ->options([
                        'privacy' => 'Privacy',
                        'terms' => 'Terms',
                        'about' => 'About',
                        'custom' => 'Custom',
                    ])
                    ->required(),
                TextInput::make('meta_title')
                    ->maxLength(255),
                TextInput::make('meta_description')
                    ->maxLength(255),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('gallery')
                            ->label('Gallery images')
                            ->image()
                            ->multiple()
                            ->directory('page-gallery')
                            ->reorderable()
                            ->dehydrated(false),
                        FileUpload::make('documents')
                            ->label('Documents')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->multiple()
                            ->directory('page-documents')
                            ->dehydrated(false),
                    ])
                    ->collapsible(),
            ]);
    }
}
