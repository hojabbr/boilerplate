<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive pages are hidden from the public site.'),
                Toggle::make('show_in_navigation')
                    ->label('Show in navigation')
                    ->default(false),
                Toggle::make('show_in_footer')
                    ->label('Show in footer')
                    ->default(false),
                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Order within nav/footer (lower first).'),
                TextInput::make('meta_title')
                    ->maxLength(255),
                TextInput::make('meta_description')
                    ->maxLength(255),
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Gallery images')
                            ->image()
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->conversion('thumb'),
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
