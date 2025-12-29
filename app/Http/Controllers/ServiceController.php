<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        $serviceDatas = Service::with(['car', 'car.user'])
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            })
            ->orderBy('date', 'desc')
            ->get();

        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        /*
        $serviceDatas = Service::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        */
        return Inertia::render('ServiceTracker', [
            'carDatas'      => $carDatas,
            'serviceDatas'  => $serviceDatas,
        ]);
    }

    /**
     * Új szerviz bejegyzés létrehozása.
     * 
        * Validálja a bejövő adatokat, ellenőrzi a jogosultságot (tulajdonos vagy admin),
        * majd elmenti az új szervizelést. Siker esetén visszairányít üzenettel.
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
            $car = Car::findOrFail($validated['car_id']);

            if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult erre a műveletre!');
            }

            Service::create($validated);
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    /**
     * Szerviz bejegyzés törlése.
     * 
        * Betölti a szerviz rekordot autóval együtt, ellenőrzi, hogy a tulajdonos vagy admin töröl-e,
        * majd törli a rekordot. Ha a szerviz nem található, ModelNotFoundException kivételt dob.
     * 
     * @param  int  $id A törlendő szerviz azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás sikerüzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a szerviz nem található
     */
    public function destroy($id) {
        $selectedServiceData = Service::with('car')->findOrFail($id);

        //$car = Car::findOrFail($selectedServiceData->car_id);
        if ($selectedServiceData->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Nem vagy jogosult erre a műveletre!');
        }

        $selectedServiceData->delete();
        return redirect()->back()->with('message', 'Sikeres törlés!');
    }

    /**
     * Meglévő szerviz bejegyzés frissítése.
     * 
        * Validálja a bejövő adatokat, betölti a szerviz rekordot autóval együtt,
        * ellenőrzi a jogosultságot (tulajdonos vagy admin), majd frissíti az adatokat.
        * Sikeres frissítés esetén visszairányít üzenettel, hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum
     * @param  int  $id A frissítendő szerviz azonosítója
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Ha a szerviz nem található
     */
    public function update(Request $request, $id) {
        $validated =  $request->validate([
            'car_id'        => 'required',
            'date'          => 'required|date',
            'current_km'    => 'required|numeric',
            'description'   => 'required|string',
            'cost'          => 'required|numeric',
        ]);

        try {
            $serviceData = Service::with('car')->findOrFail($id);

            if($serviceData->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult erre a műveletre!');
            }

            $serviceData->update($validated);
            return redirect()->back()->with('message', 'Sikeres Adatmódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba adatmódosítás közben: ' . $e->getMessage());
        }
    }
}
