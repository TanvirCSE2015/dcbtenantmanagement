<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotOwner extends Model
{
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
