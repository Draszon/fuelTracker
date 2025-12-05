<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fuel extends Model
{
    protected $fillable = ['car_id', 'date', 'name', 'quantity', 'km', 'consumption', 'money'];

    public function car(): BelongsTo {
        return $this->belongsTo(Car::class);
    }

    #[Scope]
    protected function month(Builder $query, Carbon $date) {
        return $query
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month);
    }

    #[Scope]
    protected function year(Builder $query, Carbon $date) {
        return $query
            ->whereYear('date', $date->year);
    }
}
