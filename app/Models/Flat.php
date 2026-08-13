<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
