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

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'km' => 'required|numeric|min:0.1',
            'money' => 'required|numeric|min:0',
        ]);

        try {
            Fuel::create($request->all());
            return back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
        
    }
}
