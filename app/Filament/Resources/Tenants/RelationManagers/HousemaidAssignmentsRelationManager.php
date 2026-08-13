<?php

namespace App\Filament\Resources\Tenants\RelationManagers;

use App\Models\HousemaidAssignment;
use App\Models\Staff;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HousemaidAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'housemaidAssignments';

    protected static ?string $title = 'গৃহকর্মী';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('গৃহকর্মী');
    }

    public static function getPluralModelLabel(): string
    {
        return ('গৃহকর্মীগণ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('গৃহকর্মীর তথ্য')
                ->schema([

                    TextInput::make('housemaid_name')
                        ->label(__('formlabel.housemaid_name'))
                        ->required(),

                    TextInput::make('housemaid_mobile')
                        ->label(__('formlabel.housemaid_mobile')),

                    TextInput::make('housemaid_nid')
                        ->label(__('formlabel.nid_no')),

                    FileUpload::make('housemaid_photo')
                        ->label(__('formlabel.photo'))
                        ->disk('public')
                        ->directory('images/staffs'),

                ]),

            Section::make('গৃহকর্মী নিয়োগ')
                ->schema([

                    DatePicker::make('start_date')
                        ->label(__('formlabel.start_date'))
                        ->required(),

                    DatePicker::make('end_date')
                        ->label(__('formlabel.end_date')),

                    Toggle::make('is_active')
                        ->label(__('formlabel.is_active'))
                        ->default(true),

                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                 TextColumn::make('staff.full_name')
                    ->label(__('formlabel.housemaid_name'))
                    ->searchable(),

                TextColumn::make('staff.nid_no')
                    ->label(__('formlabel.nid_no'))
                    ->searchable(),

                TextColumn::make('staff.mobile')
                    ->label(__('formlabel.mobile'))
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label(__('formlabel.is_active'))
                    ->boolean(),

                TextColumn::make('start_date')
                    ->label(__('formlabel.start_date'))
                    ->date(),

                TextColumn::make('end_date')
                    ->label(__('formlabel.end_date'))
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->icon(Heroicon::Plus)
                    ->using(function (array $data) {

                        $staff = Staff::create([
                            'staff_type' => 'housemaid',

                            'full_name'  => $data['housemaid_name'],

                            'mobile'     => $data['housemaid_mobile'] ?? null,

                            'nid_no'     => $data['housemaid_nid'] ?? null,

                            'photo'      => $data['housemaid_photo'] ?? null,
                        ]);

                        return HousemaidAssignment::create([
                            'rental_agreement_id' =>
                                $this->ownerRecord->currentAgreement->id,

                            'staff_id'   => $staff->id,

                            'start_date' => $data['start_date'],

                            'end_date'   => $data['end_date'] ?? null,

                            'is_active'  => $data['is_active'],
                        ]);
                    }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                ->fillForm(function (HousemaidAssignment $record): array {

                    return [
                        'housemaid_name'   => $record->staff?->full_name,
                        'housemaid_mobile' => $record->staff?->mobile,
                        'housemaid_nid'    => $record->staff?->nid_no,
                        'housemaid_photo'  => $record->staff?->photo,

                        'start_date'       => $record->start_date,
                        'end_date'         => $record->end_date,
                        'is_active'        => $record->is_active,
                    ];
                })

                ->using(function (HousemaidAssignment $record, array $data) {

                    $record->staff->update([
                        'full_name' => $data['housemaid_name'],
                        'mobile'    => $data['housemaid_mobile'] ?? null,
                        'nid_no'    => $data['housemaid_nid'] ?? null,
                        'photo'     => $data['housemaid_photo'] ?? null,
                    ]);

                    $record->update([
                        'start_date' => $data['start_date'],
                        'end_date'   => $data['end_date'] ?? null,
                        'is_active'  => $data['is_active'],
                    ]);

                    return $record;
                })
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
