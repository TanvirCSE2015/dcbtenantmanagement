<?php

namespace App\Filament\Resources\Plots\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('formlabel.plot_info'))
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                TextInput::make('plot_no')
                                    ->label(__('formlabel.plot_no'))
                                    ->required(),
                                TextInput::make('road_no')
                                    ->label(__('formlabel.road_no'))
                                    ->default(null),
                                TextInput::make('block')
                                    ->label(__('formlabel.block'))
                                    ->default(null),
                                TextInput::make('land_size')
                                    ->label(__('formlabel.land_size'))
                                    ->numeric()
                                    ->default(null)
                                    ->suffix('  স্কয়ার ফিট'),
                                
                                TextInput::make('area')
                                    ->label(__('formlabel.area'))
                                    ->default(null),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
