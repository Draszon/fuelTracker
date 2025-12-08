<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuelController extends Controller
{
    public function index() {
        $fuelDatas = Fuel::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        
        return Inertia::render('FuelTracker', [
            'fuelDatas' => $fuelDatas,
            'carDatas' => $carDatas,
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'car_id' => 'required',
            'date' => 'required|date',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'km' => 'required|numeric|min:0.1',
            'consumption' => 'required|numeric',
            'money' => 'required|numeric|min:0',
        ]);

        try {
            Fuel::create($request->all());
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        $selectedToDelete = Fuel::findOrFail($id);
        $selectedToDelete->delete();
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'date' => 'required|date',
            'car_id' => 'required',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'km' => 'required|numeric|min:0.1',
            'consumption' => 'required|numeric',
            'money' => 'required|numeric|min:0',
        ]);

        try {
            $fuelData = Fuel::findOrFail($id);
            $fuelData->update($validated);
            return redirect()->back()->with('message', 'Sikeres adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba módosítás közben: ' . $e->getMessage());
        }
    }
}
