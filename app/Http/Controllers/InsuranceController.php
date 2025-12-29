<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

/**
 * Biztosítások kezelését végző controller.
 * 
 * Ez a controller felelős a járművek biztosításainak CRUD műveleteiért,
 * valamint a biztosítások megjelenítéséhez szükséges adatok szolgáltatásáért.
 */
class InsuranceController extends Controller
{
    /**
     * Biztosítások listázó nézet megjelenítése.
     * 
     * Lekéri az összes biztosítást a hozzájuk tartozó autókkal együtt,
     * valamint az összes autót a rendszerben.
     * 
     * @return \Inertia\Response Inertia válasz a InsuranceTracker nézettel és adatokkal
     */
    public function index() {
        $insuranceDatas = Insurance::with(['car', 'car.user'])
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->whereHas('car', fn ($car) => $car->where('user_id', Auth::id()));
            })->get();
            
        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        /*
        $insuranceDatas = Insurance::with('car')->get();
        $carDatas = Car::all();*/

        return Inertia::render('InsuranceTracker', [
            'insuranceDatas'    => $insuranceDatas,
            'carDatas'          => $carDatas,
        ]);
    }

    /**
     * Új biztosítási bejegyzés létrehozása.
     * 
     * Validálja a bejövő adatokat és elmenti az új biztosítást az adatbázisba.
     * Sikeres mentés esetén visszairányít üzenettel, hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'car_id'            => 'required',
            'insturance_type'   => 'required|string',
            'provider'          => 'required|string',
            'cost'              => 'required|integer',
            'valid_from'        => 'required|date',
            'valid_until'       => 'required|date',
            'notes'             => 'required|string',
        ]);

        try {
            $car = Car::findOrFail($validated['car_id']);
            if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult ehhez a művelethez!');
            }

            Insurance::create($validated);
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba történt az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    /**
     * Biztosítási bejegyzés törlése.
     * 
     * Megkeresi a megadott azonosítójú biztosítást és törli az adatbázisból.
     * Ha a biztosítás nem található, ModelNotFoundException kivételt dob.
     * 
     * @param  int  $id A törlendő biztosítás azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás sikerüzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a biztosítás nem található
     */
    public function destroy($id) {
        $selectedToDestroy = Insurance::with('car')->findOrFail($id);

        if ($selectedToDestroy->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Nem vagy jogosult ehhez a művelethez!');
        }

        $selectedToDestroy->delete();
        return redirect()->back()->with('message', 'Sikeres törlés!');
    }

    /**
     * Meglévő biztosítási bejegyzés frissítése.
     * 
     * Validálja a bejövő adatokat, megkeresi a megadott azonosítójú biztosítást
     * és frissíti az adatait. Sikeres frissítés esetén visszairányít üzenettel,
     * hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum
     * @param  int  $id A frissítendő biztosítás azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a biztosítás nem található
     */
    public function update(Request $request, $id) {
        $validated = $request->validate([
            'car_id'            => 'required',
            'insturance_type'   => 'required|string',
            'provider'          => 'required|string',
            'cost'              => 'required|integer',
            'valid_from'        => 'required|date',
            'valid_until'       => 'required|date',
            'notes'             => 'required|string',
        ]);

        try {
            $selectedData = Insurance::with('car')->findOrFail($id);
            if ($selectedData->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult ehhez a művelethez!');
            }

            $targetCar = Car::findOrFail($validated['car_id']);
            if ($targetCar->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult ehhez a művelethez!');
            }

            $selectedData->update($validated);
            return redirect()->back()->with('message', 'Sikeres adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok frissítése közben: ' . $e->getMessage());
        }
        
    }
}
