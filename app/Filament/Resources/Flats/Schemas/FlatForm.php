<?php

namespace App\Filament\Resources\Flats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FlatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ফ্ল্যাটের তথ্য')
                    ->schema([
                    TextInput::make('plot_no')
                        ->label(__('formlabel.plot_no'))
                        ->required()
                        ->formatStateUsing(fn ($record) => $record->floor->building->plot->plot_no ?? null)
                        ->disabled(),
                    TextInput::make('building_name')
                        ->label(__('formlabel.building_name'))
                        ->required()
                        ->formatStateUsing(fn ($record) => $record->floor->building->building_name ?? null)
                        ->disabled(),
                    TextInput::make('floor_id')
                        ->label(__('formlabel.floor_no'))
                        ->required()
                        ->formatStateUsing(fn ($record) => $record->floor->floor_name ?? null),
                    TextInput::make('flat_no')
                        ->label(__('formlabel.flat_no'))
                        ->required(),
                    TextInput::make('flat_side')
                        ->label(__('formlabel.flat_side'))
                        ->default(null),
                    TextInput::make('flat_area')
                        ->label(__('formlabel.flat_area'))
                        ->numeric()
                        ->default(null)
                        ->suffix('  স্কয়ার ফিট'),
                ])
                ->columns(6)
                ->columnSpan('full'),
            ]);
    }
}
