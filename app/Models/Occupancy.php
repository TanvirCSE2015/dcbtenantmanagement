<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occupancy extends Model
{
    public function flat()
    {
        return $this->belongsTo(Flat::class);
    }

    public function ownerOccupancy()
    {
        return $this->hasOne(OwnerOccupancy::class);
    }

    public function rentalAgreement()
    {
        return $this->hasOne(RentalAgreement::class);
    }
}
