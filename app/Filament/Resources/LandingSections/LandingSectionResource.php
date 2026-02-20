<?php

namespace App\Filament\Resources\LandingSections;

use App\Filament\Resources\LandingSections\Pages\CreateLandingSection;
use App\Filament\Resources\LandingSections\Pages\EditLandingSection;
use App\Filament\Resources\LandingSections\Pages\ListLandingSections;
use App\Filament\Resources\LandingSections\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\LandingSections\Schemas\LandingSectionForm;
use App\Filament\Resources\LandingSections\Tables\LandingSectionsTable;
use App\Models\LandingSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class LandingSectionResource extends Resource
{
    use Translatable;

    protected static ?string $model = LandingSection::class;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LandingSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingSections::route('/'),
            'create' => CreateLandingSection::route('/create'),
            'edit' => EditLandingSection::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
