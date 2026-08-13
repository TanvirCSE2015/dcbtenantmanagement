<?php

namespace App\Filament\Resources\Flats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('floor.building.plot.plot_no')
                    ->label(__('formlabel.plot_no'))
                    ->searchable(),
                TextColumn::make('floor.building.building_name')
                    ->label(__('formlabel.building_name'))
                    ->searchable(),
                TextColumn::make('floor.floor_name')
                    ->label(__('formlabel.floor_name'))
                    ->searchable(),
                TextColumn::make('flat_no')
                    ->label(__('formlabel.flat_no'))
                    ->searchable(),
                TextColumn::make('flat_side')
                    ->label(__('formlabel.flat_side'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'North' => 'উত্তর',
                        'South' => 'দক্ষিণ',
                        'East'  => 'পূর্ব',
                        'West'  => 'পশ্চিম',
                        default => $state ?? '-',
                    })
                    ->searchable(),
                TextColumn::make('flat_area')
                    ->label(__('formlabel.flat_area'))
                    ->numeric()
                    ->sortable()
                    ->suffix('  স্কয়ার ফিট'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
