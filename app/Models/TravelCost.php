<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelCost extends Model
{
    protected $fillable = [
        'date',
        'direction',
        'distance',
        'travel_expenses',
    ];

    public function car ():BelongsTo {
        return $this->belongsTo(Car::class);
    }
}
