<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\EmergencyContact;
use App\Models\Occupancy;
use App\Models\RentalAgreement;
use App\Models\TenantProfession;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Previous Occupancy Close
            |--------------------------------------------------------------------------
            */

            Occupancy::where('flat_id', $data['flat_no'])
                ->where('is_current', true)
                ->update([
                    'is_current' => false,
                    'end_date'   => now(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | Tenant Create
            |--------------------------------------------------------------------------
            */

            $tenant = static::getModel()::create([
                'tenant_name'   => $data['tenant_name'],
                'father_name'   => $data['father_name'],
                'mother_name'   => $data['mother_name'],
                'date_of_birth' => $data['date_of_birth'],
                'birth_place' => $data['birth_place'],
                'religion' => $data['religion'],
                'education' => $data['education'],
                'marital_status' => $data['marital_status'],
                'nid_no'        => $data['nid_no'],
                'passport_no'   => $data['passport_no'],
                'mobile'        => $data['mobile'],
                'email' => $data['email'],
                // 'profession'    => $data['profession'],
                'photo'         => $data['photo'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Occupancy Create
            |--------------------------------------------------------------------------
            */

            $occupancyType = $data['occupancy_type'];

            $occupancy = Occupancy::create([
                'flat_id'        => $data['flat_no'],
                'occupancy_type' => $occupancyType,
                'start_date'     => $data['agreement_start_date'] ?? now()->format('Y-m-d'),
                'end_date'       => $data['agreement_end_date'] ?? '9999-12-31',
                'is_current'     => true,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Rental Agreement Create
            |--------------------------------------------------------------------------
            */
            if ($occupancyType === 'tenant'){

                $agreement = RentalAgreement::create([
                    'occupancy_id'          => $occupancy->id,
                    'tenant_id'             => $tenant->id,
                    'agreement_no'          => $data['agreement_no'],
                    'agreement_start_date'  => $data['agreement_start_date'],
                    'agreement_end_date'    => $data['agreement_end_date'],
                    'monthly_rent'          => $data['monthly_rent'] ?? null,
                    'security_deposit'      => $data['security_deposit'] ?? null,
                    'status'                => 'active',
                ]);
            }else{
                 /*
                |--------------------------------------------------------------------------
                | Owner Internal Agreement
                |--------------------------------------------------------------------------
                */
                $agreementNo = null;
                do {

                    $agreementNo =
                        'OWN-' .
                        now()->format('Ym') .
                        '-' .
                        rand(1000, 9999);

                } while (
                    RentalAgreement::where(
                        'agreement_no',
                        $agreementNo
                    )->exists()
                );

                $agreement = RentalAgreement::create([
                    'occupancy_id'          => $occupancy->id,
                    'tenant_id'             => $tenant->id,
                    'agreement_no'          => $agreementNo,
                    'agreement_start_date'  => now()->format('Y-m-d'),
                    'agreement_end_date'    => '9999-12-31',
                    'monthly_rent'          => null,
                    'security_deposit'      => null,
                    'status'                => 'active',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Emergency Contact Create
            |--------------------------------------------------------------------------
            */

            EmergencyContact::create([
                'rental_agreement_id' => $agreement->id,
                'name'                => $data['name'],
                'relation'            => $data['relation'],
                'mobile'              => $data['mobile'],
                'address'             => $data['address'] ?? null,
            ]);

            TenantProfession::create([
                'rental_agreement_id' => $agreement->id,
                'title_p'                => $data['title_p'],
                'office_name'            => $data['office_name'],
                'designation'              => $data['designation'],
                'mobile_no'             => $data['mobile_no'] ?? null,
                'office_address'             => $data['office_address'] ?? null,
            ]);

            return $tenant;
        });
    }
}
