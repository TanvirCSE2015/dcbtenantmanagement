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
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantFamilyMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'tenantFamilyMembers';

    protected static ?string $title = 'পরিবারের সদস্য';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('পরিবারের সদস্য');
    }

    public static function getPluralModelLabel(): string
    {
        return ('পরিবারের সদস্যগণ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                ->schema([
                    TextInput::make('name')
                        ->label(__('formlabel.name'))
                        ->required(),
                    TextInput::make('relation')
                        ->label(__('formlabel.relation'))
                        ->required(),
                    TextInput::make('nid_no')
                        ->label(__('formlabel.nid_no'))
                        ->default(null),
                    TextInput::make('education')
                        ->label(__('formlabel.education'))
                        ->required(),
                    TextInput::make('mobile')
                        ->label(__('formlabel.mobile'))
                        ->default(null),
                    DatePicker::make('date_of_birth')
                        ->label(__('formlabel.date_of_birth')),
                ])
                ->columns(3)
                ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                ->label(__('formlabel.name'))
                    ->searchable(),
                TextColumn::make('relation')
                ->label(__('formlabel.relation'))
                    ->searchable(),
                TextColumn::make('nid_no')
                ->label(__('formlabel.nid_no'))
                    ->searchable(),
                TextColumn::make('education')
                ->label(__('formlabel.education'))
                    ->searchable(),
                TextColumn::make('mobile')
                ->label(__('formlabel.mobile'))
                    ->searchable(),
                TextColumn::make('date_of_birth')
                ->label(__('formlabel.date_of_birth'))
                    ->date()
                    ->sortable(),
                // TextColumn::make('created_at')
                // ->label(__('formlabel.date_of_birth'))
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
                 DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
