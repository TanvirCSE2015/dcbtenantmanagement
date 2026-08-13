<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }
}
