<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuelController extends Controller
{
    //az alap oldalon megjelenő adatok lekérése és átküldése a nézetnek
    public function index() {
        $fuelDatas = Fuel::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        
        return Inertia::render('FuelTracker', [
            'fuelDatas' => $fuelDatas,
            'carDatas' => $carDatas,
        ]);
    }

    //üzemanyag feltöltése esetén validálom a beérkező adatokat
    //a hozzá tartozó kocsiadatokat lekérdezem a beküldött car_id alapján
    //kiszámolom, hogy mennyi a feltöltés után a jelenlegi km óra állása
    public function store(Request $request) {
        $car = Car::find($request->car_id);
        $validated = $request->validate([
            'car_id'        => 'required',
            'date'          => 'required|date',
            'name'          => 'required|string',
            'quantity'      => 'required|numeric|min:0',
            'km'            => 'required|numeric|min:0.1',
            'consumption'   => 'required|numeric',
            'money'         => 'required|numeric|min:0',
        ]);

        try {
            $car->current_km += $request->km;
            $car->save();
            Fuel::create($validated);
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    //törlöm a kijelölt adatot, de előtte mivel törlök viszsa kell írnom 
    //a beír km-t a jelenlegi km órára, mert ez az adat mégsem létezett
    public function destroy(Request $request, $id) {
        $car = Car::find($request->car_id);
        $car->current_km -= $request->km;
        $car->save();
        $selectedToDelete = Fuel::findOrFail($id);
        $selectedToDelete->delete();
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    public function update(Request $request, $id) {        
        $validated = $request->validate([
            'date'          => 'required|date',
            'car_id'        => 'required',
            'name'          => 'required|string',
            'quantity'      => 'required|numeric|min:0',
            'km'            => 'required|numeric|min:0',
            'consumption'   => 'required|numeric',
            'money'         => 'required|numeric|min:0',
        ]);
        //FONTOS: MÉG AZT KELL MEGOLDANI, HOGY AMIKOR FRISSÍTI AZ
        //ADATOKAT AKKOR A CURRENT KM A MÓDOSÍTÁSSAL EGYÜTT NÖVEKEDJEN VAGY CSÖKKENJEN!!!
        try {
            $car = Car::find($request->car_id);
            $fuelData = Fuel::findOrFail($id);

            $delta = $validated["km"] - $fuelData->km;
            $car->current_km += $delta;
            $car->save();

            $fuelData->update($validated);
            return redirect()->back()->with('message', 'Sikeres adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba módosítás közben: ' . $e->getMessage());
        }
    }
}
