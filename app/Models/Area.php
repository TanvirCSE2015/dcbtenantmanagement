<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    public function plots(): HasMany
    {
        return $this->hasMany(Plot::class, 'area_id');
    }
}
