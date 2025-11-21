<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuelController extends Controller
{
    public function index() {
        $fuelDatas = Fuel::orderBy('date', 'desc')->get();
        return Inertia::render('FuelTracker', [
            'fuelDatas' => $fuelDatas,
        ]);
    }
}
