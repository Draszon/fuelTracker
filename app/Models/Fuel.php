<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    protected $fillable = ['date', 'name', 'quantity', 'km', 'consumption', 'money'];
}
