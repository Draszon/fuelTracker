<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index() {
        $carDatas = Car::all();
        
        return Inertia::render('CarTracker', [
            'carDatas' => $carDatas,
        ]);
    }
}
