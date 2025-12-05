<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    #[Scope]
    protected function month(Builder $query, Carbon $date) {
        return $query
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month);
    }

    protected function year(Builder $query, Carbon $date) {
        return $query
            ->whereYear('date', $date->year);
    }
}
