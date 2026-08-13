<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAssignment extends Model
{
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vechicle::class);
    }
}
