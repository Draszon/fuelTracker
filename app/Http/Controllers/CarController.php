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

    public function store(Request $request) {
        $request->validate([
            'name'                      => 'required|string',
            'licence_plate'             => 'required|string',
            'car_type'                  => 'required|string',
            'average_fuel_consumption'  => 'required|numeric',
            'year'                      => 'required|numeric',
            'oil_change_cycle_km'       => 'required|numeric',
            'last_oil_change_km'        => 'required|numeric',
            'oil_change_cycle_year'     => 'required|numeric',
            'last_oil_change_date'      => 'required|date',
            'break_oil_cycle_year'      => 'required|numeric',
            'last_break_oil_change_date' => 'required|date',
            'inspection_valid_until'    => 'required|date',
            'inspection_valid_from'     => 'required|date',
        ]);

        try {
            Car::create($request->all());
            return redirect()->back()->with('message', 'Sikeres mentés');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba a feltöltés közben: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        $selected = Car::findOrFail($id);
        $selected->delete();
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name'                      => 'required|string',
            'licence_plate'             => 'required|string',
            'car_type'                  => 'required|string',
            'average_fuel_consumption'  => 'required|numeric',
            'year'                      => 'required|numeric',
            'oil_change_cycle_km'       => 'required|numeric',
            'last_oil_change_km'        => 'required|numeric',
            'oil_change_cycle_year'     => 'required|numeric',
            'last_oil_change_date'      => 'required|date',
            'break_oil_cycle_year'      => 'required|numeric',
            'last_break_oil_change_date' => 'required|date',
            'inspection_valid_until'    => 'required|date',
            'inspection_valid_from'     => 'required|date',
        ]);

        try {
            $selectedCar = Car::findOrFail($id);
            $selectedCar->update($validated);
            return redirect()->back()->with('message', 'Sikeres módosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba a módosításban: ' . $e->getMessage());
        }
        
    }
}
