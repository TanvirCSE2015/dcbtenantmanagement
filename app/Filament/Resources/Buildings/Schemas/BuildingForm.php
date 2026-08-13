<?php

namespace App\Filament\Resources\Buildings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('formlabel.building_info'))
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextInput::make('plot_id')
                                    ->label(__('formlabel.plot_no'))
                                    ->required()
                                    ->formatStateUsing(fn($record) => $record?->plot?->plot_no),
                                TextInput::make('building_name')
                                    ->label(__('formlabel.building_name'))
                                    ->required(),
                                TextInput::make('total_floor')
                                    ->label(__('formlabel.total_floor'))
                                    ->required()
                                    ->numeric(),
                                TextInput::make('total_flat')
                                    ->label(__('formlabel.total_flat'))
                                    ->required()
                                    ->numeric(),
                                TextInput::make('lift_count')
                                    ->label(__('formlabel.lift_count'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
