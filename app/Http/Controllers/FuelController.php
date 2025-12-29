<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class FuelController extends Controller
{
    /**
     * Üzemanyag nyilvántartó alapoldal megjelenítése.
     *
     * - Lekéri a tankolásokat dátum szerint csökkenő sorrendben
     * - Betölti a kapcsolódó autót és annak tulajdonosát
     * - Nem admin esetén csak a saját autók tankolásai kerülnek be
     * - Átküldi az adatokat az Inertia nézetnek
     */
    public function index() {
        $fuelDatas = Fuel::with(['car', 'car.user'])
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            })
            ->orderBy('date', 'desc')
            ->get();

        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();
            
        return Inertia::render('FuelTracker', [
            'fuelDatas' => $fuelDatas,
            'carDatas' => $carDatas,
        ]);
    }

    /**
     * Új üzemanyag bejegyzés létrehozása.
     *
     * - Ellenőrzi, hogy az autó a felhasználóé vagy az admin rögzít
     * - Tranzakcióban növeli az autó km-állását és létrehozza a tankolást
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

                if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult erre a műveletre!');
                }

                // A tankolás növeli az autó aktuális km óra állását.
                $car->current_km += $request->km;
                $car->save();
                Fuel::create($validated);
            });
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben.' . $e->getMessage());
        }
    }
    
    /**
     * Üzemanyag bejegyzés törlése.
     *
     * - Csak a tulajdonos vagy admin törölhet
     * - Tranzakcióban visszavonja a tankolás km-hatását, majd töröl
     */
    public function destroy(Request $request, $id) {
        DB::transaction(function() use($request, $id) {
            $car = Car::findOrFail($request->car_id);
            $selectedToDelete = Fuel::findOrFail($id);

            if ($selectedToDelete->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult erre a műveletre!');
            }

            // A törölt tankolás km értékét levonjuk az aktuális állásból.
            $car->current_km -= $request->km;
            $car->save();
            
            $selectedToDelete->delete();
        });
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    /**
     * Üzemanyag bejegyzés frissítése.
     *
     * - Csak a tulajdonos vagy admin módosíthat
     * - A km különbséget számolja el, hogy a km-óra állás pontos maradjon
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

                if ($fuelData->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult erre a műveletre!');
                }

                // Csak a különbséget számoljuk el, hogy ne duplázzuk a km óra állást.
                $delta = $validated["km"] - $fuelData->km;
                $car->current_km += $delta;
                $car->save();

                $fuelData->update($validated);
            });
            
            return redirect()->back()->with('message', 'Sikeres adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba módosítás közben: ' . $e->getMessage());
        }
    }
}
