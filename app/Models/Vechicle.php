<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vechicle extends Model
{
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }

    public function driverAssignments()
    {
        return $this->hasMany(DriverAssignment::class);
    }
}
