<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantProfession extends Model
{
    public function rentalAggrement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }
}
