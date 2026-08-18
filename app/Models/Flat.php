<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Flat extends Model
{
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function owners()
    {
        return $this->hasMany(FlatOwner::class);
    }

    public function occupancies()
    {
        return $this->hasMany(Occupancy::class);
    }

    public function ownershipTransfers(): MorphMany
    {
        return $this->morphMany(
            OwnershipTransfer::class,
            'ownable'
        );
    }

    public function currentOwners()
    {
        return $this->hasMany(FlatOwner::class)
            ->where('is_current', true);
    }
}
