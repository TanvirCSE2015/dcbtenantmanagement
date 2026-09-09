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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArmsRelationManager extends RelationManager
{
    protected static string $relationship = 'arms';

    protected static ?string $title = 'আগ্নেয়াস্ত্র';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('আগ্নেয়াস্ত্র');
    }

    public static function getPluralModelLabel(): string
    {
        return ('আগ্নেয়াস্ত্রের তালিকা');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('rental_agreement_id')
                //     ->required()
                //     ->numeric(),
                Grid::make(3)
                    ->schema([
                        Select::make('arms_category')
                            ->label(__('formlabel.arms_category'))
                            ->options([
                                'পিস্তল' => 'পিস্তল',
                                'রাইফেল' => 'রাইফেল',
                                'শর্টগান' => 'শর্টগান',
                                'রিবালবার' => 'রিবালবার',
                            ])
                            ->required(),
                        TextInput::make('arms_number')
                            ->label(__('formlabel.arms_number'))
                            ->required(),
                        TextInput::make('arms_reg_number')
                            ->label(__('formlabel.arms_reg_number'))
                            ->required(),
                       
                ])
                ->columnSpanFull(),
                Grid::make(4)
                    ->schema([
                        DatePicker::make('validity_date')
                                    ->label(__('formlabel.validity_date')),
                        TextInput::make('ammunition_details')
                            ->default(null)
                            ->label(__('formlabel.ammunition_details')),
                        TextInput::make('issued_from')
                            ->default(null)
                            ->label(__('formlabel.issued_from')),
                        Toggle::make('is_active')
                            ->label(__('formlabel.is_active'))
                            ->required()
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('arms_number')
            ->columns([
                // TextColumn::make('rental_agreement_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('arms_category')
                    ->label(__('formlabel.arms_category'))
                    ->searchable(),
                TextColumn::make('arms_number')
                    ->label(__('formlabel.arms_number'))
                    ->searchable(),
                TextColumn::make('validity_date')
                    ->label(__('formlabel.validity_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('ammunition_details')
                    ->label(__('formlabel.ammunition_details'))
                    ->searchable(),
                TextColumn::make('issued_from')
                    ->label(__('formlabel.issued_from'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('formlabel.is_active'))
                    ->boolean(),
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
