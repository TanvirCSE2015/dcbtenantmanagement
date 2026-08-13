<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public function rentalAgreements()
    {
        return $this->hasMany(RentalAgreement::class);
    }

    public function currentAgreement()
    {
        return $this->hasOne(RentalAgreement::class)
            ->where('status', 'active');
    }

    public function tenantFamilyMembers()
    {
        return $this->hasManyThrough(
            TenantFamilyMember::class,
            RentalAgreement::class,
            'tenant_id',
            'rental_agreement_id',
            'id',
            'id'
        );
    }

    public function vechicles()
    {
        return $this->hasManyThrough(
            Vechicle::class,
            RentalAgreement::class,
            'tenant_id',
            'rental_agreement_id',
            'id',
            'id'
        );
    }

    public function driverAssignments()
    {
        return $this->hasManyThrough(
            DriverAssignment::class,
            RentalAgreement::class,
            'tenant_id',
            'rental_agreement_id',
            'id',
            'id'
        );
    }

    public function housemaidAssignments()
    {
        return $this->hasManyThrough(
            HousemaidAssignment::class,
            RentalAgreement::class,
            'tenant_id',
            'rental_agreement_id',
            'id',
            'id'
        );
    }
}
