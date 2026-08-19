<?php

namespace App\Filament\Resources\Flats;

use App\Filament\Resources\Flats\Pages\CreateFlat;
use App\Filament\Resources\Flats\Pages\EditFlat;
use App\Filament\Resources\Flats\Pages\ListFlats;
use App\Filament\Resources\Flats\RelationManagers\OccupanciesRelationManager;
use App\Filament\Resources\Flats\RelationManagers\OwnershipTransfersRelationManager;
use App\Filament\Resources\Flats\RelationManagers\OwnersRelationManager;
use App\Filament\Resources\Flats\Schemas\FlatForm;
use App\Filament\Resources\Flats\Tables\FlatsTable;
use App\Models\Flat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FlatResource extends Resource
{
    protected static ?string $model = Flat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    // protected static string|UnitEnum|null $navigationGroup  = 'সম্পত্তি ব্যবস্থাপনা';
    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return ('ফ্ল্যাট');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ফ্ল্যাটসমূহ');
    }

    public static function form(Schema $schema): Schema
    {
        return FlatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FlatsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OwnersRelationManager::class,
            OwnershipTransfersRelationManager::class,
            OccupanciesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlats::route('/'),
            'create' => CreateFlat::route('/create'),
            'edit' => EditFlat::route('/{record}/edit'),
        ];
    }
}
