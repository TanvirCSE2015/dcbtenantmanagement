<?php

namespace App\Filament\Resources\Tenants\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_name')
                    ->label(__('formlabel.tenant_name'))
                    ->searchable(),
                TextColumn::make('father_name')
                    ->label(__('formlabel.father_name'))
                    ->searchable(),
                TextColumn::make('mother_name')
                    ->label(__('formlabel.mother_name'))
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->label(__('formlabel.date_of_birth'))
                    ->date()
                    ->sortable(),
                TextColumn::make('nid_no')
                    ->label(__('formlabel.nid_no'))
                    ->searchable(),
                TextColumn::make('passport_no')
                    ->label(__('formlabel.passport_no'))
                    ->searchable(),
                TextColumn::make('mobile')
                    ->label(__('formlabel.mobile'))
                    ->searchable(),
                TextColumn::make('profession')
                    ->label(__('formlabel.profession'))
                    ->searchable(),
                TextColumn::make('photo')
                    ->label(__('formlabel.photo'))
                    ->searchable(),
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
                EditAction::make()
                    ->label(''),
                Action::make('print')
                    ->icon(Heroicon::Printer)
                    ->color('success')
                    ->label('')
                    ->url(fn ($record) => route('single-tenant.print', [
                        'tenant' => $record->id,
                    ]))
            ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
