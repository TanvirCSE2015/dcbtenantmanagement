<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\DriverAssignment;
use App\Models\HousemaidAssignment;
use App\Models\Occupancy;
use App\Models\RentalAgreement;
use App\Models\TenantFamilyMember;
use App\Models\Vechicle;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('vacate')
                ->label('ফ্ল্যাট খালি করুন')
                ->icon('heroicon-o-home')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {

                    $agreement = $this->record->currentAgreement;

                    if (! $agreement) {
                        return;
                    }

                    $occupancy = $agreement->occupancy;

                    $agreement->update([
                        'status' => 'terminated',
                        'agreement_end_date' => now(),
                    ]);

                    $occupancy->update([
                        'is_current' => false,
                        'end_date' => now(),
                    ]);

                    Occupancy::create([
                        'flat_id' => $occupancy->flat_id,
                        'occupancy_type' => 'vacant',
                        'start_date' => now(),
                        'is_current' => true,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('ফ্ল্যাট সফলভাবে খালি করা হয়েছে')
                        ->send();
                }),
                 Action::make('renewAgreement')
                ->label('চুক্তি নবায়ন')
                ->icon('heroicon-o-arrow-path')
                ->color('success')

                ->schema([
                    Section::make('ভাড়াটিয়া নবায়নের চুক্তিপত্র')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('agreement_no')
                                ->label('চুক্তি নম্বর')
                                ->required(),

                            DatePicker::make('agreement_start_date')
                                ->label('চুক্তি শুরুর তারিখ')
                                ->required(),

                            DatePicker::make('agreement_end_date')
                                ->label('চুক্তি শেষের তারিখ')
                                ->required(),

                            TextInput::make('monthly_rent')
                                ->label('মাসিক ভাড়া')
                                ->numeric(),

                            TextInput::make('security_deposit')
                                ->label('সিকিউরিটি ডিপোজিট')
                                ->numeric(),
                            FileUpload::make('agreement_file')
                                ->label('চুক্তিপত্র')
                                ->disk('public')
                                ->directory('images/agreement_file')

                        ]),
                ])

                ->requiresConfirmation()

                ->action(function (array $data) {

                    DB::transaction(function () use ($data) {

                        $tenant = $this->record;

                        $oldAgreement = $tenant->currentAgreement;

                        if (! $oldAgreement) {
                            throw new \Exception('Active Agreement not found.');
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Previous Agreement Close
                        |--------------------------------------------------------------------------
                        */

                        $oldAgreement->update([
                            'status' => 'renewed',
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | New Agreement Create
                        |--------------------------------------------------------------------------
                        */

                        $newAgreement = RentalAgreement::create([

                            'occupancy_id' =>
                                $oldAgreement->occupancy_id,

                            'tenant_id' =>
                                $tenant->id,

                            'previous_agreement_id' =>
                                $oldAgreement->id,

                            'agreement_no' =>
                                $data['agreement_no'],

                            'agreement_start_date' =>
                                $data['agreement_start_date'],

                            'agreement_end_date' =>
                                $data['agreement_end_date'],

                            'monthly_rent' =>
                                $data['monthly_rent'] ?? null,

                            'security_deposit' =>
                                $data['security_deposit'] ?? null,

                            'status' => 'active',
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | Copy Active Drivers
                        |--------------------------------------------------------------------------
                        */

                        $drivers = $oldAgreement->driverAssignments()
                            ->where('is_active', true)
                            ->get()
                            ->map(function ($driver) use ($newAgreement) {

                                return [
                                    'rental_agreement_id' => $newAgreement->id,
                                    'staff_id'            => $driver->staff_id,
                                    'vechicle_id'         => $driver->vechicle_id,
                                    'start_date'          => now()->toDateString(),
                                    'end_date'            => null,
                                    'is_active'           => true,
                                    'created_at'          => now(),
                                    'updated_at'          => now(),
                                ];
                            })
                            ->toArray();

                        if (! empty($drivers)) {
                            DriverAssignment::insert($drivers);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Copy Active Housemaids
                        |--------------------------------------------------------------------------
                        */

                        $housemaids = $oldAgreement->housemaidAssignments()
                            ->where('is_active', true)
                            ->get()
                            ->map(function ($housemaid) use ($newAgreement) {

                                return [
                                    'rental_agreement_id' => $newAgreement->id,
                                    'staff_id'            => $housemaid->staff_id,
                                    'start_date'          => now()->toDateString(),
                                    'end_date'            => null,
                                    'is_active'           => true,
                                    'created_at'          => now(),
                                    'updated_at'          => now(),
                                ];
                            })
                            ->toArray();

                        if (! empty($housemaids)) {
                            HousemaidAssignment::insert($housemaids);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Copy Vehicles
                        |--------------------------------------------------------------------------
                        */

                        $vehicles = $oldAgreement->vechicles
                            ->map(function ($vehicle) use ($newAgreement) {

                                return [
                                    'rental_agreement_id' => $newAgreement->id,
                                    'vehicle_type'        => $vehicle->vehicle_type,
                                    'brand'               => $vehicle->brand,
                                    'model'               => $vehicle->model,
                                    'registration_no'     => $vehicle->registration_no,
                                    'color'               => $vehicle->color,
                                    'created_at'          => now(),
                                    'updated_at'          => now(),
                                ];
                            })
                            ->toArray();

                        if (! empty($vehicles)) {
                            Vechicle::insert($vehicles);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Copy Family Members
                        |--------------------------------------------------------------------------
                        */

                        $members = $oldAgreement->familyMembers
                            ->map(function ($member) use ($newAgreement) {

                                return [
                                    'rental_agreement_id' => $newAgreement->id,
                                    'name'                => $member->name,
                                    'relation'            => $member->relation,
                                    'nid_no'              => $member->nid_no,
                                    'education'           => $member->education,
                                    'mobile'              => $member->mobile,
                                    'date_of_birth'       => $member->date_of_birth,
                                    'created_at'          => now(),
                                    'updated_at'          => now(),
                                ];
                            })
                            ->toArray();

                        if (! empty($members)) {
                            TenantFamilyMember::insert($members);
                        }
                    });

                    Notification::make()
                        ->success()
                        ->title('চুক্তি সফলভাবে নবায়ন করা হয়েছে')
                        ->send();
                }),
            DeleteAction::make()
                ->icon(Heroicon::Trash),
            
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $agreement = $this->record
            ->currentAgreement;

        if ($agreement) {

            $data['agreement_no'] = $agreement->agreement_no;
            $data['agreement_start_date'] = $agreement->agreement_start_date;
            $data['agreement_end_date'] = $agreement->agreement_end_date;
            $data['monthly_rent'] = $agreement->monthly_rent;
            $data['security_deposit'] = $agreement->security_deposit;

            if ($agreement->emergencyContact) {

                $data['name']
                    = $agreement->emergencyContact->name;

                $data['relation']
                    = $agreement->emergencyContact->relation;

                $data['mobile']
                    = $agreement->emergencyContact->mobile;

                $data['address']
                    = $agreement->emergencyContact->address;
            }
        }

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model {

        DB::transaction(function () use ($record, $data) {

            $record->update([
                'tenant_name' => $data['tenant_name'],
                'father_name' => $data['father_name'],
                'mother_name' => $data['mother_name'],
                'mobile' => $data['mobile'],
                'profession' => $data['profession'],
            ]);

            $agreement = $record->currentAgreement;

            if ($agreement) {
                $occupancy = $agreement->occupancy;

                if ($occupancy) {
                    $data['flat_id'] = $occupancy->flat_id;  
                    
                }
                $agreement->update([
                    'agreement_no' => $data['agreement_no'],
                    'agreement_start_date' => $data['agreement_start_date'],
                    'agreement_end_date' => $data['agreement_end_date'],
                    'monthly_rent' => $data['monthly_rent'],
                    'security_deposit' => $data['security_deposit'],
                ]);

                $agreement->emergencyContact()?->update([
                    'name' => $data['name'],
                    'relation' => $data['relation'],
                    'mobile' => $data['mobile'],
                    'address' => $data['address'],
                    'status' => $data['status'],
                ]);
            }
        });

        return $record;
    }
}
