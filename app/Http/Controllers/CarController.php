<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * Kocsik nyilvántartó oldal megjelenítése.
     * 
     * - Lekéri az összes kocsit tulajdonossal együtt
     * - Nem admin esetén csak a saját autók jelennek meg
     * - Átküldi az adatokat az Inertia nézetnek
     */
    public function index() {
        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('user_id', 'desc')
            ->get();

        return Inertia::render('CarTracker', [
            'carDatas'  => $carDatas,
            'userId'    => Auth::id(),
        ]);
    }

    /**
     * Kocsi adatainak tárolásáért felel.
     * 
     * - Minden mezőt validál
     * - Az új autót automatikusan az aktuális felhasználóhoz rendeli
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name'                      => 'required|string',
            'licence_plate'             => 'required|string',
            'car_type'                  => 'required|string',
            'average_fuel_consumption'  => 'required|numeric',
            'year'                      => 'required|numeric',
            'current_km'                => 'required|numeric',
            'oil_change_cycle_km'       => 'required|numeric',
            'last_oil_change_km'        => 'required|numeric',
            'oil_change_cycle_year'     => 'required|numeric',
            'last_oil_change_date'      => 'required|date',
            'break_oil_cycle_year'      => 'required|numeric',
            'last_break_oil_change_date' => 'required|date',
            'inspection_valid_until'    => 'required|date',
            'inspection_valid_from'     => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();

        try {
            Car::create($validated);
            return redirect()->back()->with('message', 'Sikeres mentés');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba a feltöltés közben: ' . $e->getMessage());
        }
    }
    
    /**
     * Kocsik törléséért felel az adatbázisból.
     * 
     * - Csak a tulajdonos vagy admin törölhet
     * - Id alapján keresi meg a kocsit és eltávolítja
     */
    public function destroy($id) {
        $selected = Car::findOrFail($id);

        if ($selected->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Nem vagy jogosult erre a műveletre!');
        }

        $selected->delete();
        return redirect()->back()->with('message', 'Sikeres adattörlés!');
    }

    /**
     * A kocsik adatainak frissítéséért felel.
     * 
     * - Csak a tulajdonos vagy admin módosíthat
     * - Minden mezőt validál, majd frissít
     */
    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name'                      => 'required|string',
            'licence_plate'             => 'required|string',
            'car_type'                  => 'required|string',
            'average_fuel_consumption'  => 'required|numeric',
            'year'                      => 'required|numeric',
            'current_km'                => 'required|numeric',
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

            if ($selectedCar->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult erre a műveletre!');
            }

            $selectedCar->update($validated);
            return redirect()->back()->with('message', 'Sikeres módosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba a módosításban: ' . $e->getMessage());
        }
        
    }
}
