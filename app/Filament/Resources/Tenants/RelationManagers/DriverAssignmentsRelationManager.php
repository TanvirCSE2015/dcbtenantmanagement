<?php

namespace App\Filament\Resources\Tenants\RelationManagers;

use App\Models\DriverAssignment;
use App\Models\Staff;
use App\Models\Vechicle;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'driverAssignments';

    protected static ?string $title = 'ড্রাইভার';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('ড্রাইভার');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ড্রাইভারগণ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                    Section::make('ড্রাইভারের তথ্য')
                    ->schema([
                    TextInput::make('driver_name')
                        ->label(__('formlabel.driver_name'))
                        ->required(),

                    TextInput::make('driver_mobile')
                        ->label(__('formlabel.driver_mobile')),

                    TextInput::make('driver_nid')
                        ->label(__('formlabel.nid_no')),

                    FileUpload::make('driver_photo')
                        ->disk('public')
                        ->directory('images/drivers')
                        ->label(__('formlabel.photo')),
                ])
                ->columns(2)
                ->columnSpanFull(),
                Section::make('ড্রাইভার নিয়োগ')
                ->schema([
                    Select::make('vechicle_id')
                        ->label('যানবাহন')
                        ->options(
                            Vechicle::where(
                                'rental_agreement_id',
                                $this->ownerRecord->currentAgreement->id
                            )->pluck('registration_no', 'id')
                        )
                        ->searchable()
                        ->required(),

                    DatePicker::make('start_date')
                        ->label(__('formlabel.start_date'))
                        ->required(),

                    DatePicker::make('end_date')
                        ->label(__('formlabel.end_date')),

                    Toggle::make('is_active')
                        ->label(__('formlabel.is_active'))
                        ->default(true),
                ])
                ->columns(2)
                ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('staff.full_name')
                    ->label(__('formlabel.driver_name'))
                    ->searchable(),

                TextColumn::make('staff.nid_no')
                    ->label(__('formlabel.nid_no'))
                    ->searchable(),

                TextColumn::make('staff.mobile')
                    ->label(__('formlabel.mobile'))
                    ->searchable(),

                TextColumn::make('vechicle.registration_no')
                    ->label(__('formlabel.registration_no')),

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
                        'staff_type' => 'driver',
                        'full_name'  => $data['driver_name'],
                        'mobile'     => $data['driver_mobile'] ?? null,
                        'nid_no'     => $data['driver_nid'] ?? null,
                        'photo'      => $data['driver_photo'] ?? null,
                    ]);

                    return DriverAssignment::create([
                        'rental_agreement_id' =>
                            $this->ownerRecord->currentAgreement->id,

                        'staff_id'   => $staff->id,

                        'vechicle_id' => $data['vechicle_id'],

                        'start_date' => $data['start_date'],

                        'end_date'   => $data['end_date'] ?? null,

                        'is_active'  => $data['is_active'],
                    ]);
                }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->fillForm(function (DriverAssignment $record): array {

                        return [
                            'driver_name'   => $record->staff?->full_name,
                            'driver_mobile' => $record->staff?->mobile,
                            'driver_nid'    => $record->staff?->nid_no,
                            'driver_photo'  => $record->staff?->photo,

                            'vechicle_id'   => $record->vechicle_id,
                            'start_date'    => $record->start_date,
                            'end_date'      => $record->end_date,
                            'is_active'     => $record->is_active,
                        ];
                    })

                    ->using(function (DriverAssignment $record, array $data) {

                        $record->staff->update([
                            'full_name' => $data['driver_name'],
                            'mobile'    => $data['driver_mobile'] ?? null,
                            'nid_no'    => $data['driver_nid'] ?? null,
                            'photo'     => $data['driver_photo'] ?? null,
                        ]);

                        $record->update([
                            'vechicle_id' => $data['vechicle_id'],
                            'start_date'  => $data['start_date'],
                            'end_date'    => $data['end_date'] ?? null,
                            'is_active'   => $data['is_active'],
                        ]);

                        return $record;
                    }),
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
