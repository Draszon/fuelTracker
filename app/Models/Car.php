<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'name',
        'licence_plate',
        'car_type',
        'year',
        'oil_change_cycle_km',
        'oil_change_cycle_year',
        'break_oil_cycle_km',
    ];
}
