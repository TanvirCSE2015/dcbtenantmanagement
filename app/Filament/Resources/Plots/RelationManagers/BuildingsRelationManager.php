<?php

namespace App\Filament\Resources\Plots\RelationManagers;

use App\Filament\Resources\Buildings\BuildingResource;
use App\Filament\Resources\Plots\PlotResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BuildingsRelationManager extends RelationManager
{
    protected static string $relationship = 'buildings';

    protected static ?string $relatedResource = PlotResource::class;

     protected static ?string $title = 'ভবন';

     protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('ভবন');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ভবনগুলি');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                TextInput::make('building_name')
                    ->label(__('formlabel.building_name'))
                    ->required(),

                TextInput::make('total_flat')
                    ->label(__('formlabel.total_flat'))
                    ->numeric()
                    ->required(),
                TextInput::make('total_floor')
                    ->label(__('formlabel.total_floor'))
                    ->numeric()
                    ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('building_name')
                    ->label(__('formlabel.building_name')),

                TextColumn::make('total_floor')
                    ->label(__('formlabel.total_floor')),

            ])
            
            ->recordUrl(
                fn ($record): string =>
                    BuildingResource::getUrl('edit', ['record' => $record])
            )
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus),
            ]);
    }
}
