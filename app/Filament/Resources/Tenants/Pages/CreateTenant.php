<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\EmergencyContact;
use App\Models\Occupancy;
use App\Models\RentalAgreement;
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
                'nid_no'        => $data['nid_no'],
                'passport_no'   => $data['passport_no'],
                'mobile'        => $data['mobile'],
                'profession'    => $data['profession'],
                'photo'         => $data['photo'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Occupancy Create
            |--------------------------------------------------------------------------
            */

            $occupancy = Occupancy::create([
                'flat_id'        => $data['flat_no'],
                'occupancy_type' => 'tenant',
                'start_date'     => $data['agreement_start_date'],
                'end_date'       => $data['agreement_end_date'],
                'is_current'     => true,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Rental Agreement Create
            |--------------------------------------------------------------------------
            */

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

            return $tenant;
        });
    }
}
