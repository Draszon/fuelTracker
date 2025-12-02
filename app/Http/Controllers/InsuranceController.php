<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsuranceController extends Controller
{
    public function index() {
        $insuranceDatas = Insurance::with('car')->get();
        $carDatas = Car::all();
        return Inertia::render('InsuranceTracker', [
            'insuranceDatas' => $insuranceDatas,
            'carDatas' => $carDatas,
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'car_id' => 'required',
            'insturance_type' => 'required|string',
            'provider' => 'required|string',
            'cost' => 'required|integer',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date',
            'notes' => 'required|string',
        ]);

        try {
            Insurance::create($request->all());
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba történt az adatok feltöltése közben: ' . $e->getMessage());
        }
    }
}
