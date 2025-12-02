<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurance extends Model
{
    protected $fillable = [
        'car_id',
        'insturance_type',
        'provider',
        'cost',
        'valid_from',
        'valid_until',
        'notes',
    ];

    public function car():BelongsTo {
        return $this->belongsTo(Car::class);
    }
}
