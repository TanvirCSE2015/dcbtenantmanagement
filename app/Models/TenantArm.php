<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantArm extends Model
{
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }
}
