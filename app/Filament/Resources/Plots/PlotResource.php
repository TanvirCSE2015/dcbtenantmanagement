<?php

namespace App\Filament\Resources\Plots;

use App\Filament\Resources\Plots\Pages\CreatePlot;
use App\Filament\Resources\Plots\Pages\EditPlot;
use App\Filament\Resources\Plots\Pages\ListPlots;
use App\Filament\Resources\Plots\RelationManagers\BuildingsRelationManager;
use App\Filament\Resources\Plots\RelationManagers\OwnersRelationManager;
use App\Filament\Resources\Plots\Schemas\PlotForm;
use App\Filament\Resources\Plots\Tables\PlotsTable;
use App\Models\Plot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlotResource extends Resource
{
    protected static ?string $model = Plot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::SquaresPlus;
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return ('প্লট');
    }

    public static function getPluralModelLabel(): string
    {
        return ('প্লটসমূহ');
    }

    public static function form(Schema $schema): Schema
    {
        return PlotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OwnersRelationManager::class,
            BuildingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlots::route('/'),
            'create' => CreatePlot::route('/create'),
            'edit' => EditPlot::route('/{record}/edit'),
        ];
    }
}
