<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fuel extends Model
{
    protected $fillable = ['car_id', 'date', 'name', 'quantity', 'km', 'consumption', 'money'];

    public function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }
}
