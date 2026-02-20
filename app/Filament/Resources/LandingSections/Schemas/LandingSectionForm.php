<?php

namespace App\Filament\Resources\LandingSections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LandingSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
                        'hero' => 'Hero',
                        'features' => 'Features',
                        'testimonials' => 'Testimonials',
                        'latest_posts' => 'Latest blog posts',
                        'cta' => 'CTA',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active on landing page')
                    ->default(true),
                TextInput::make('title')
                    ->maxLength(255),
                TextInput::make('subtitle')
                    ->maxLength(255),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('cta_text')
                    ->maxLength(255),
                TextInput::make('cta_url')
                    ->url()
                    ->maxLength(255),
                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->collection('image')
                            ->conversion('thumb'),
                    ])
                    ->collapsible(),
            ]);
    }
}
