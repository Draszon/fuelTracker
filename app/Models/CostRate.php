<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostRate extends Model
{
    protected $fillable = [
        'fuel_price',
        'amortization_price',
    ];
}
