<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousemaidAssignment extends Model
{
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
