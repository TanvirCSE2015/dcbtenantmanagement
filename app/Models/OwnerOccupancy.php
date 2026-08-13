<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerOccupancy extends Model
{
    public function occupancy()
    {
        return $this->belongsTo(Occupancy::class);
    }

    public function flatOwner()
    {
        return $this->belongsTo(FlatOwner::class);
    }
}
