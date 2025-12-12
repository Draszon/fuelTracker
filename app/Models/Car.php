<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'name',
        'licence_plate',
        'car_type',
        'average_fuel_consumption',
        'year',
        'current_km',
        'oil_change_cycle_km',
        'oil_change_cycle_year',
        'break_oil_cycle_year',
        'inspection_valid_from',
        'inspection_valid_until',
        'last_oil_change_km',
        'last_oil_change_date',
        'last_break_oil_change_date',
    ];

    public function fuels(): HasMany {
        return $this->hasMany(Fuel::class);
    }

    public function services(): HasMany {
        return $this->hasMany(Service::class);
    }

    public function insurances(): HasMany {
        return $this->hasMany(Insurance::class);
    }
}
