<?php

namespace App\Filament\Resources\Tenants\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VechiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vechicles';

    protected static ?string $title = 'যানবাহন';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('যানবাহন');
    }

    public static function getPluralModelLabel(): string
    {
        return ('যানবাহনগুলি');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        TextInput::make('vehicle_type')
                            ->label(__('formlabel.vehicle_type'))
                            ->required(),
                        TextInput::make('brand')
                            ->label(__('formlabel.brand'))
                            ->default(null),
                        TextInput::make('model')
                            ->label(__('formlabel.model'))
                            ->default(null),
                        TextInput::make('registration_no')
                            ->label(__('formlabel.registration_no'))
                            ->required(),
                        TextInput::make('color')
                            ->label(__('formlabel.color'))
                            ->default(null),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('registration_no')
            ->columns([
                TextColumn::make('vehicle_type')
                    ->label(__('formlabel.vehicle_type'))
                    ->searchable(),
                TextColumn::make('brand')
                    ->label(__('formlabel.brand'))
                    ->searchable(),
                TextColumn::make('model')
                    ->label(__('formlabel.model'))
                    ->searchable(),
                TextColumn::make('registration_no')
                    ->label(__('formlabel.registration_no'))
                    ->searchable(),
                TextColumn::make('color')
                    ->label(__('formlabel.color'))
                    ->searchable(),
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
            ->headerActions([
                CreateAction::make()
                ->icon(Heroicon::Plus)
                ->mutateDataUsing(function (array $data): array {

                    $data['rental_agreement_id'] =
                        $this->ownerRecord->currentAgreement->id;

                    return $data;
                }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
