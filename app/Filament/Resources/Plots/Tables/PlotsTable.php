<?php

namespace App\Filament\Resources\Plots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plot_no')
                    ->label(__('formlabel.plot_no'))
                    ->searchable(),
                TextColumn::make('road_no')
                    ->label(__('formlabel.road_no'))
                    ->searchable(),
                TextColumn::make('block')
                    ->label(__('formlabel.block'))
                    ->searchable(),
                TextColumn::make('area.area_name')
                    ->label(__('formlabel.area'))
                    ->searchable(),
                TextColumn::make('buildings_count')
                ->counts('buildings')
                ->label(__('formlabel.buildings'))
                    ->suffix('  টি'), 

                TextColumn::make('owners_count')
                ->counts('owners')
                ->label(__('formlabel.owners'))
                    ->suffix('  জন'),
                TextColumn::make('land_size')
                    ->label(__('formlabel.land_size'))
                    ->numeric()
                    ->sortable()
                    ->suffix('  স্কয়ার ফিট'),
                TextColumn::make('created_at')
                    ->label(__('formlabel.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('formlabel.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
