<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'car_id',
        'date',
        'current_km',
        'description',
        'cost',
    ];

    public function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }
}
