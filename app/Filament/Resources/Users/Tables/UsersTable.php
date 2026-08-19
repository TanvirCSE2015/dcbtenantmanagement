<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile')
                    ->label('মোবাইল')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('ইমেইল')
                    ->searchable(),

                TextColumn::make('current_plot_count')
                    ->label('বর্তমান প্লট')
                    ->getStateUsing(function ($record) {
                        return $record->currentPlotOwners()->count();
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('current_flat_count')
                    ->label('বর্তমান ফ্ল্যাট')
                    ->getStateUsing(function ($record) {
                        return $record->currentFlatOwners()->count();
                    })
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
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
