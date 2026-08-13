<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlatOwner extends Model
{
    public function flat()
    {
        return $this->belongsTo(Flat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ownerOccupancies()
    {
        return $this->hasMany(OwnerOccupancy::class);
    }
}
