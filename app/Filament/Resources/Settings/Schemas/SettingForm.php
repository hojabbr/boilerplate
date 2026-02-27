<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
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
                    ->disabledOn('edit'),
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
                Section::make('Branding')
                    ->description('Upload branding assets. Leave empty to use the default files from /public/favicon/.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('app_logo')
                            ->label('App logo')
                            ->collection('app_logo')
                            ->image()
                            ->visibility('public')
                            ->helperText('Displayed in the admin sidebar and on the frontend. SVG or PNG recommended.'),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->label('Favicon (.ico or .png)')
                            ->collection('favicon')
                            ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'image/png'])
                            ->visibility('public')
                            ->helperText('Browser tab icon. Use a 32×32 or 48×48 .ico or .png.'),
                        SpatieMediaLibraryFileUpload::make('apple_touch_icon')
                            ->label('Apple touch icon')
                            ->collection('apple_touch_icon')
                            ->image()
                            ->visibility('public')
                            ->helperText('180×180 PNG shown when added to iOS home screen.'),
                        SpatieMediaLibraryFileUpload::make('manifest_icon_192')
                            ->label('PWA icon 192×192')
                            ->collection('manifest_icon_192')
                            ->image()
                            ->visibility('public')
                            ->helperText('192×192 PNG for the web app manifest.'),
                        SpatieMediaLibraryFileUpload::make('manifest_icon_512')
                            ->label('PWA icon 512×512')
                            ->collection('manifest_icon_512')
                            ->image()
                            ->visibility('public')
                            ->helperText('512×512 PNG for the web app manifest.'),
                    ])
                    ->visible(fn ($livewire) => $livewire->getRecord()?->getAttribute('key') === 'site')
                    ->columns(2)
                    ->collapsed(),
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
            ]);
    }
}
