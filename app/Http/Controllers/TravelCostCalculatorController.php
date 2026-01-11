<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CostRate;
use App\Models\TravelCost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use DB;

class TravelCostCalculatorController extends Controller
{
	/**
	 * Alap nézet betöltése és havi összesítések kiszámítása.
	 *
	 * Betölti a költségárakat, utazási adatokat és autókat.
	 * Kiszámítja az aktuális hónapra vonatkozó összesített km-et, amortizációs költséget és havi költségösszeget.
	 *
	 * @return \Inertia\Response
	 */
    public function index () {
		// költségárak betöltése (fuel_price, amortization_price stb.)
        $costs = CostRate::all();
		// aktuális user és admin státusz
        $user = Auth::user();
        $isAdmin = (bool)($user->is_admin ?? false);
        $now = Carbon::now();

		// alap lekérdezés (nem-admin esetén csak a saját autók adatait hozza)
        $baseQuery = TravelCost::with(['car', 'car.user'])
            ->when(!$isAdmin, function ($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            });

		// havi összes km
        $monthlyKm = (clone $baseQuery)
            ->whereMonth('date', $now->month)
            ->pluck('distance')
            ->sum();
        
		// teljes lista dátum szerint csökkenő
        $travelDatas = (clone $baseQuery)->orderBy('date', 'desc')->get();

		// autók betöltése (szintén korlátozva nem-admin esetén)
        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

		// amortizáció számítása: havi km * egységár
        $amortizationCost = $monthlyKm * $costs[0]->amortization_price;

		// havi egyéb utazási költségek és összegzés
        $monthlyCost = (clone $baseQuery)
            ->whereMonth('date', $now->month)
            ->pluck('travel_expenses')
            ->sum();
        $monthlyCostSum = $monthlyCost + $amortizationCost;

        return Inertia::render('TravelCostCalculator', [
            'costs'             => $costs,
            'travelDatas'       => $travelDatas,
            'carDatas'          => $carDatas,
            'monthlyKm'         => $monthlyKm,
            'amortizationCost'  => $amortizationCost,
            'monthlyCostSum'    => $monthlyCostSum,
        ]);
    }

	/**
	 * Szűrt adatok visszaadása (pl. kiválasztott hónap és autó).
	 *
	 * Feldolgozza a request-ben kapott params tömböt és kiszámítja a kiválasztott hónap/autó összesítéseit.
	 * Ha nincs megadva month/car, az adott összegző változók nem lesznek definiálva (jelenleg nincs külön alapérték kezelés).
	 *
	 * @param Request $request
	 * @return \Inertia\Response
	 */
    public function filteredDatas(Request $request) {
		$params = $request->input('params');
        $costs = CostRate::all();
        $user = Auth::user();
        $is_admin = (bool)($user->is_admin ?? false);
        $now = Carbon::now();

		// alap lekérdezés, nem-admin esetén korlátozva
        $baseQuery = TravelCost::with('car', 'car.user')
            ->when(!$is_admin, function($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            });

        $travelDatas = (clone $baseQuery)->orderBy('date', 'desc')->get();

		// autók listája (szűrés nem-admin esetén)
        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

		// ha meg van adva hónap és autó, kiszámoljuk a kiválasztott havi értékeket
        if ($params['month'] && $params['car']) {
            $selectedMonthKm = (clone $baseQuery)
                ->whereMonth('date', $params['month'])
                ->where('car_id', $params['car'])
                ->pluck('distance')
                ->sum();
            
            // amortizáció a kiválasztott havi km alapján
            $amortizationCost = $selectedMonthKm * $costs[0]->amortization_price;
            
            $monthlyCost = (clone $baseQuery)
                ->whereMonth('date', $params['month'])
                ->where('car_id', $params['car'])
                ->pluck('travel_expenses')
                ->sum();
            $selectedMonthCostSum = $monthlyCost + $amortizationCost;
        }

        return Inertia::render('TravelCostCalculator', [
            'costs' => $costs,
            'travelDatas' => $travelDatas,
            'carDatas' => $carDatas,
            'monthlyKm' => $selectedMonthKm,
            'amortizationCost'  => $amortizationCost,
            'monthlyCostSum'    => $selectedMonthCostSum,
        ]);
    }

	/**
	 * Új utazási adat mentése.
	 *
	 * Validálja a bemenetet, ellenőrzi a jogosultságot (autó tulajdonosa vagy admin),
	 * kiszámítja az utazási költséget és létrehozza a TravelCost rekordot.
	 *
	 * @param Request $request
	 */
    public function storeTravelData (Request $request) {
		// bemeneti validáció
        $validated = $request->validate([
            'car_id'    => 'required',
            'date'      => 'required|date',
            'direction' => 'required|string',
            'distance'  => 'required|numeric',
        ]);

        try {
            DB::transaction(function() use($validated, $request) {
				// jogosultság ellenőrzése: csak az autó tulajdonosa vagy admin hozhat létre bejegyzést
                $car = Car::findOrFail($request->car_id);
                if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult ehhez a művelethet!');
                }

				// üzemanyagár és autó fogyasztás lekérése
                $fuelCost = CostRate::where('id', '1')
                    ->value('fuel_price');
                $carConsumption = Car::where('id', $validated['car_id'])
                    ->value('average_fuel_consumption');

				// költség számítása: (km * l/100) * üzemanyagár
                $travelExpenses = round((($validated['distance'] * $carConsumption) / 100) * $fuelCost);
                TravelCost::create([
                    'car_id'            => $validated['car_id'],
                    'date'              => $validated['date'],
                    'direction'         => $validated['direction'],
                    'distance'          => $validated['distance'],
                    'travel_expenses'   => $travelExpenses,
                    'fuel_costs'        => $fuelCost,
                ]);
                return back()->with('message', 'Sikeres adatfeltöltés!');
            });
        } catch (\Exception $e) {
            return back()->with('message', 'Hiba történt az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

	/**
	 * Üzemanyagár frissítése.
	 *
	 * Validálja a fuel_price mezőt és frissíti a CostRate táblát.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function updateFuelPrice (Request $request) {
		// validáció
        $validated = $request->validate([
            'fuel_price' => 'required|numeric',
        ]);

        try {
            $fuelPrice = CostRate::find(1);
            $fuelPrice->fuel_price = $validated['fuel_price'];
            $fuelPrice->save();

            return back()->with('message', 'Sikeres frissítés!');
        } catch (\Exception $e) {
            return back()->with('message', 'Hiba az adatok frissítése közben: ' . $e->getMessage());
        }
    }

	/**
	 * Amortizációs egységár frissítése.
	 *
	 * Validálja az amortization_price mezőt és frissíti a CostRate rekordot.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function updateAmortizationPrice (Request $request) {
		// validáció
        $validated = $request->validate([
            'amortization_price' => 'required|numeric',
        ]);

        try {
            $fuelPrice = CostRate::find(1);
            $fuelPrice->amortization_price = $validated['amortization_price'];
            $fuelPrice->save();

            return back()->with('message', 'Sikeres frissítés!');
        } catch (\Exception $e) {
            return back()->with('message', 'Hiba az adatok frissítése közben: ' . $e->getMessage());
        }
    }

	/**
	 * Utazási adat törlése jogosultság ellenőrzéssel.
	 *
	 * Csak az autó tulajdonosa vagy admin törölheti a bejegyzést.
	 *
	 * @param int $id
	 */
    public function destroyTravelData ($id) {
        DB::transaction(function () use($id) {
            $selectedToDelete = TravelCost::findOrFail($id);

			// jogosultság ellenőrzés
            if ($selectedToDelete->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult ehhez a művelethez!');
            }

            $selectedToDelete->delete();

            return back()->with('message', 'Sikere adattörlés!');
        });
    }

	/**
	 * Utazási adat frissítése.
	 *
	 * Validálja a bemenetet, ellenőrzi a jogosultságot (a kiválasztott autó alapján),
	 * újraszámolja az utazási költséget és frissíti a TravelCost rekordot.
	 *
	 * @param Request $request
	 * @param int $id
	 */
    public function updateTravelData (Request $request, $id) {
		// validáció
        $validated = $request->validate([
            'car_id'    => 'required',
            'date'      => 'required|date',
            'direction' => 'required|string',
            'distance'  => 'required|numeric',
        ]);

        try {
            DB::transaction(function() use($validated, $request, $id) {
                $travelData = TravelCost::findOrFail($id);

				// jogosultság ellenőrzés a megadott autó alapján
                $car = Car::findOrFail($request->car_id);
                if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult ehhez a művelethet!');
                }

				// új költség számítása és mentés
                $fuelCost = CostRate::where('id', '1')
                    ->value('fuel_price');
                $carConsumption = Car::where('id', $validated['car_id'])
                    ->value('average_fuel_consumption');

                $travelExpenses = round((($validated['distance'] * $carConsumption) / 100) * $fuelCost);
                $travelData->update([
                    'car_id'            => $validated['car_id'],
                    'date'              => $validated['date'],
                    'direction'         => $validated['direction'],
                    'distance'          => $validated['distance'],
                    'travel_expenses'   => $travelExpenses,
                    'fuel_costs'        => $fuelCost,
                ]);
                return back()->with('message', 'Sikeres adatfeltöltés!');
            });
        } catch (\Exception $e) {
            return back()->with('message', 'Hiba az adatok frissítése közben: ' . $e->getMessage());
        }
        
    }
}
