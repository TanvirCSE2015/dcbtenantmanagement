<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalAgreement extends Model
{
    public function occupancy()
    {
        return $this->belongsTo(Occupancy::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function previousAgreement()
    {
        return $this->belongsTo(
            RentalAgreement::class,
            'previous_agreement_id'
        );
    }

    public function renewals()
    {
        return $this->hasMany(
            RentalAgreement::class,
            'previous_agreement_id'
        );
    }

    public function familyMembers()
    {
        return $this->hasMany(TenantFamilyMember::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vechicle::class);
    }

    public function driverAssignments()
    {
        return $this->hasMany(DriverAssignment::class);
    }

    public function housemaidAssignments()
    {
        return $this->hasMany(HousemaidAssignment::class);
    }

    public function emergencyContacts()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    // public function previousAddresses()
    // {
    //     return $this->hasMany(PreviousAddress::class);
    // }
}
