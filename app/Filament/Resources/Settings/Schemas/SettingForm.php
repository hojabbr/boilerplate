<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->disabledOn('edit')
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        Section::make('Company')
                            ->schema([
                                TextInput::make('company_name')
                                    ->maxLength(255),
                                TextInput::make('tagline')
                                    ->maxLength(255),
                                TextInput::make('address')
                                    ->maxLength(65535),
                            ]),
                        Section::make('Contact')
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                        Section::make('Social')
                            ->schema([
                                KeyValue::make('social_links'),
                            ]),
                        Section::make('Blog')
                            ->description('Override blog config. Leave empty to use config/env defaults.')
                            ->schema([
                                TextInput::make('blog_posts_per_page')
                                    ->label('Posts per page')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->placeholder((string) config('blog.posts_per_page', 10))
                                    ->helperText('Number of posts on the public blog index.'),
                                TextInput::make('blog_translation_body_chunk_size')
                                    ->label('Translation body chunk size')
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(20000)
                                    ->placeholder((string) config('blog.translation_body_chunk_size', 6000))
                                    ->helperText('Max characters per chunk when AI translates long bodies.'),
                            ])
                            ->visible(fn ($livewire) => $livewire->getRecord()?->getAttribute('key') === 'site')
                            ->collapsed(),
                    ]),
            ]);
    }
}
