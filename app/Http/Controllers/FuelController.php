<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuelController extends Controller
{
    /**
     * Üzemanyag nyilvántartó alapoldal megjelenítése.
     *
     * - Lekéri az összes tankolási adatot dátum szerint csökkenő sorrendben
     * - Betölti a kapcsolódó autó adatokat
     * - Átküldi az adatokat az Inertia nézetnek
     */
    public function index() {
        $fuelDatas = Fuel::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        
        return Inertia::render('FuelTracker', [
            'fuelDatas' => $fuelDatas,
            'carDatas' => $carDatas,
        ]);
    }

    /**
     * Új üzemanyag bejegyzés létrehozása.
     *
     * A mentés hatással van az adott autó aktuális kilométeróra állására,
     * ezért a tankolt km érték hozzáadásra kerül az autóhoz.
     */
    public function store(Request $request) {
        
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
            DB::transaction(function() use($validated, $request) {
                $car = Car::findOrFail($request->car_id);

                //A tankolás növeli az autó aktuális km óra állását.
                $car->current_km += $request->km;
                $car->save();
                Fuel::create($validated);
            });
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben.');
        }
    }
    
    /**
     * Üzemanyag bejegyzés törlése.
     *
     * A törlés visszavonja az adott tankolás által növelt
     * kilométeróra állást, mivel az adat megszűnik.
     */
    public function destroy(Request $request, $id) {
        DB::transaction(function() use($request, $id) {
            $car = Car::findOrFail($request->car_id);

            //A törölt tankolás km érték levonjuk az aktuális állásból
            $car->current_km -= $request->km;
            $car->save();

            $selectedToDelete = Fuel::findOrFail($id);
            $selectedToDelete->delete();
        });
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    /**
     * Üzemanyag bejegyzés frissítése.
     *
     * Mivel a km érték módosulhat, csak a régi és az új km
     * különbsége kerül elszámolásra az autó aktuális állásában.
     */
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

        

        try {
            DB::transaction(function() use($request, $id, $validated) {
                $car = Car::findOrFail($request->car_id);
                $fuelData = Fuel::findOrFail($id);

                //Csak a különbséget számoljuk el, hogy ne duplázzuk a km óra állást.
                $delta = $validated["km"] - $fuelData->km;
                $car->current_km += $delta;
                $car->save();

                $fuelData->update($validated);
            });
            
            return redirect()->back()->with('message', 'Sikeres adatmódosítás!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('message', 'Hiba módosítás közben.');
        }
    }
}
