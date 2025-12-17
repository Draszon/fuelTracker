<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * StatisticsController
 *
 * Ez a controller kezeli a statisztikai adatok kiszámítását és megjelenítését.
 * Tartalmaz általános havi/éves statisztikákat üzemanyag-fogyasztásról,
 * szolgáltatásokról és karbantartási emlékeztetőkről.
 * Használja az Inertia.js-t a Vue komponensekhez való adatátadáshoz.
 */
class StatisticsController extends Controller
{
    /**
     * index() - Általános statisztikák megjelenítése
     *
     * Ez a metódus kiszámítja a jelenlegi hónap és év általános statisztikáit
     * minden autó számára. Számítja az üzemanyag-fogyasztást, költségeket,
     * átlagfogyasztást és szolgáltatási adatokat.
     * A Fuel és Service modellek local scope-jaival (month, year) szűri az adatokat.
     *
     * @return \Inertia\Response
     */
    public function index() {
        // Aktuális dátum lekérése Carbon-nal
        $now = Carbon::now();

        // Havi adatok összegzése (összes km és liter minden autóhoz)
        $sumMonthKm = Fuel::month($now)->sum('km');
        $sumMonthFuel = Fuel::month($now)->sum('quantity');

        // Éves adatok összegzése
        $sumYearKm = Fuel::year($now)->sum('km');
        $sumYearFuel = Fuel::year($now)->sum('quantity');

        // Karbantartási emlékeztetők alapértelmezett értékei (még nem számított)
        $periodicMaintenances = [
            'next_oil_change_date' => 0,
            'next_oil_change_km' => 0,
            'oil_change_km_left' => 0,
            'next_break_oil_change_date' => 0,
            'inspection_valid_until' => 0,
        ];

        // Havi üzemanyag statisztikák tömbje
        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->sum('quantity'), // Összes liter havi
            'total_km'          => Fuel::month($now)->sum('km'),       // Összes km havi
            'total_cost'        => Fuel::month($now)->sum('money'),    // Összes költség havi
            'monthly_fuel_count'=> Fuel::month($now)->count(),         // Tankolások száma havi
        ];

        // Átlagfogyasztás számítása havi (l/100km), nullázás ha nincs adat
        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        // Éves üzemanyag statisztikák tömbje
        $fuelYear = [
            'total_liter'       => Fuel::year($now)->sum('quantity'),
            'total_km'          => Fuel::year($now)->sum('km'),
            'total_cost'        => Fuel::year($now)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->count(),
        ];

        // Átlagfogyasztás számítása éves
        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        // Havi szolgáltatási statisztikák (szerviz költségek)
        $statisticsMonth = [
            'total_cost'    => Service::month($now)->sum('cost'),
            'service_count' => Service::month($now)->count(),
        ];

        // Éves szolgáltatási statisztikák
        $statisticsYear = [
            'total_cost'    => Service::year($now)->sum('cost'),
            'service_count' => Service::year($now)->count(),
        ];

        // Adatok átadása az Inertia Statistics komponensnek
        return Inertia::render('Statistics', [
            'fuelMonth'         => $fuelMonth,
            'fuelYear'          => $fuelYear,
            'statisticsMonth'   => $statisticsMonth,
            'statisticsYear'    => $statisticsYear,
            'carDatas'          => Car::all(), // Összes autó adatai
            'periodicMaintenances'  => $periodicMaintenances, // Karbantartási adatok (alapértelmezetten 0)
        ]);
    }

    /**
     * filteredStatistic() - Autóra szűrt statisztikák
     *
     * Ez a metódus egy adott autó (car_id alapján) statisztikáit számítja ki.
     * Számítja a havi/éves üzemanyag és szolgáltatási adatokat,
     * valamint a karbantartási emlékeztetőket (olajcsere, műszaki vizsga stb.).
     * A Fuel és Service modellek local scope-jaival szűri az adatokat az adott autóra.
     *
     * @param Request $request - Tartalmazza a car_id-t
     * @return \Inertia\Response
     */
    public function filteredStatistic(Request $request) {
        // Aktuális dátum
        $now = Carbon::now();

        // Kiválasztott autó adatai
        $carDatas = Car::find($request->car_id);

        // Havi adatok összegzése az adott autóhoz
        $sumMonthKm = Fuel::month($now)->where('car_id', $request->car_id)->sum('km');
        $sumMonthFuel = Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity');

        // Éves adatok összegzése az adott autóhoz
        $sumYearKm = Fuel::year($now)->where('car_id', $request->car_id)->sum('km');
        $sumYearFuel = Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity');

        // Következő olajcsere km számítása (utolsó olajcsere + ciklus)
        $next_oil_change_km = $carDatas->last_oil_change_km + $carDatas->oil_change_cycle_km;

        // Karbantartási emlékeztetők számítása
        $periodicMaintenances = [
            'next_oil_change_date' => Carbon::parse($carDatas->last_oil_change_date)
                ->addYears($carDatas->oil_change_cycle_year) // Következő olajcsere dátum
                ->toDateString(),
            'next_oil_change_km' => $next_oil_change_km, // Következő olajcsere km
            'oil_change_km_left' => $next_oil_change_km - $carDatas->current_km, // Hány km van hátra
            'next_break_oil_change_date' => Carbon::parse($carDatas->last_break_oil_change_date)
                ->addYears($carDatas->break_oil_cycle_year) // Következő fékolaj csere dátum
                ->toDateString(),
            'inspection_valid_until' => $carDatas->inspection_valid_until, // Műszaki vizsga érvényessége
        ];

        // Havi üzemanyag statisztikák az adott autóhoz
        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::month($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::month($now)->where('car_id', $request->car_id)->sum('money'),
            'monthly_fuel_count'=> Fuel::month($now)->where('car_id', $request->car_id)->count(),
        ];

        // Átlagfogyasztás számítása havi az adott autóhoz
        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        // Éves üzemanyag statisztikák az adott autóhoz
        $fuelYear = [
            'total_liter'       => Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::year($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::year($now)->where('car_id', $request->car_id)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->where('car_id', $request->car_id)->count(),
        ];

        // Átlagfogyasztás számítása éves az adott autóhoz
        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        // Havi szolgáltatási statisztikák az adott autóhoz
        $statisticsMonth = [
            'total_cost'    => Service::month($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::month($now)->where('car_id', $request->car_id)->count(),
        ];

        // Éves szolgáltatási statisztikák az adott autóhoz
        $statisticsYear = [
            'total_cost'    => Service::year($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::year($now)->where('car_id', $request->car_id)->count(),
        ];

        // Adatok átadása az Inertia Statistics komponensnek
        return Inertia::render('Statistics', [
            'fuelMonth'             => $fuelMonth,
            'fuelYear'              => $fuelYear,
            'statisticsMonth'       => $statisticsMonth,
            'statisticsYear'        => $statisticsYear,
            'carDatas'              => Car::all(), // Összes autó listája (szűréshez)
            'periodicMaintenances'  => $periodicMaintenances, // Számított karbantartási adatok
        ]);
    }
}
