<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    
    public function driverAssignments()
    {
        return $this->hasMany(DriverAssignment::class);
    }

    public function housemaidAssignments()
    {
        return $this->hasMany(HousemaidAssignment::class);
    }
}
