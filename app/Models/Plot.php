<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Plot extends Model
{
    public function owners()
    {
        return $this->hasMany(PlotOwner::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
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
        return $this->hasMany(PlotOwner::class)
            ->where('is_current', true);
    }
}
