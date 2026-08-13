<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
