<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Szervizek kezelését végző controller.
 * 
 * Ez a controller felelős a járművek szervizeléseinek CRUD műveleteiért,
 * valamint a szervizelési előzmények megjelenítéséhez szükséges adatok szolgáltatásáért.
 */
class ServiceController extends Controller
{
    /**
     * Szervizek listázó nézet megjelenítése.
     * 
     * Lekéri az összes szervizelést a hozzájuk tartozó autókkal együtt,
     * dátum szerint csökkenő sorrendben rendezve, valamint az összes autót a rendszerben.
     * 
     * @return \Inertia\Response Inertia válasz a ServiceTracker nézettel és adatokkal
     */
    public function index() {
        $serviceDatas = Service::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        return Inertia::render('ServiceTracker', [
            'carDatas'      => $carDatas,
            'serviceDatas'  => $serviceDatas,
        ]);
    }

    /**
     * Új szerviz bejegyzés létrehozása.
     * 
     * Validálja a bejövő adatokat és elmenti az új szervizelést az adatbázisba.
     * Sikeres mentés esetén visszairányít üzenettel, hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'car_id'        => 'required',
            'date'          => 'required|date',
            'current_km'    => 'required|numeric',
            'description'   => 'required|string',
            'cost'          => 'required|numeric',
        ]);

        try {
            Service::create($validated);
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    /**
     * Szerviz bejegyzés törlése.
     * 
     * Megkeresi a megadott azonosítójú szervizelést és törli az adatbázisból.
     * Ha a szerviz nem található, ModelNotFoundException kivételt dob.
     * 
     * @param  int  $id A törlendő szerviz azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás sikerüzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a szerviz nem található
     */
    public function destroy($id) {
        $selectedServiceData = Service::findOrFail($id);
        $selectedServiceData->delete();
        return redirect()->back()->with('message', 'Sikeres törlés!');
    }

    /**
     * Meglévő szerviz bejegyzés frissítése.
     * 
     * Validálja a bejövő adatokat, megkeresi a megadott azonosítójú szervizelést
     * és frissíti az adatait. Sikeres frissítés esetén visszairányít üzenettel,
     * hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum
     * @param  int  $id A frissítendő szerviz azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a szerviz nem található
     */
    public function update(Request $request, $id) {
        $request->validate([
            'car_id'        => 'required',
            'date'          => 'required|date',
            'current_km'    => 'required|numeric',
            'description'   => 'required|string',
            'cost'          => 'required|numeric',
        ]);

        try {
            $serviceData = Service::findOrFail($id);
            $serviceData->update($request->all());
            return redirect()->back()->with('message', 'Sikeres Adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba adatmódosítás közben: ' . $e->getMessage());
        }
    }
}
