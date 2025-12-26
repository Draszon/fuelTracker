<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Statisztikák kezelését végző controller.
 * 
 * Ez a controller felelős az üzemanyag-fogyasztási és szervizelési statisztikák
 * kiszámításáért és megjelenítéséért. Támogatja az általános (összes autó) és
 * autó-specifikus statisztikák lekérdezését havi és éves bontásban.
 * Karbantartási emlékeztetőket is számol (olajcsere, fékolaj csere, műszaki vizsga).
 */
class StatisticsController extends Controller
{
    /**
     * Általános statisztikák megjelenítése (összes autó).
     * 
     * Kiszámítja a jelenlegi hónap és év általános statisztikáit minden autó számára.
     * Tartalmazza az üzemanyag-fogyasztást (összes liter, km, költség),
     * átlagfogyasztást (l/100km), valamint a szervizelési adatokat (költség, darabszám).
     * A Fuel és Service modellek month() és year() scope-jaival szűri az adatokat.
     * 
     * @return \Inertia\Response Inertia válasz a Statistics nézettel és statisztikai adatokkal
     */
    public function index() {
        // Aktuális dátum lekérése
        $now = Carbon::now();

        // Havi összesítések számításához (átlagfogyasztás kalkulációhoz)
        $sumMonthKm = Fuel::month($now)->sum('km');
        $sumMonthFuel = Fuel::month($now)->sum('quantity');

        // Éves összesítések számításához
        $sumYearKm = Fuel::year($now)->sum('km');
        $sumYearFuel = Fuel::year($now)->sum('quantity');

        // Karbantartási emlékeztetők alapértelmezett értékei (szűrés nélküli nézetnél nem használt)
        $periodicMaintenances = [
            'next_oil_change_date' => 0,
            'next_oil_change_km' => 0,
            'oil_change_km_left' => 0,
            'next_break_oil_change_date' => 0,
            'inspection_valid_until' => 0,
        ];

        // Havi üzemanyag statisztikák összegyűjtése
        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->sum('quantity'),
            'total_km'          => Fuel::month($now)->sum('km'),
            'total_cost'        => Fuel::month($now)->sum('money'),
            'monthly_fuel_count'=> Fuel::month($now)->count(),
        ];

        // Havi átlagfogyasztás számítása (l/100km), nullázás ha nincs adat
        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        // Éves üzemanyag statisztikák összegyűjtése
        $fuelYear = [
            'total_liter'       => Fuel::year($now)->sum('quantity'),
            'total_km'          => Fuel::year($now)->sum('km'),
            'total_cost'        => Fuel::year($now)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->count(),
        ];

        // Éves átlagfogyasztás számítása (l/100km)
        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        // Havi szervizelési statisztikák
        $statisticsMonth = [
            'total_cost'    => Service::month($now)->sum('cost'),
            'service_count' => Service::month($now)->count(),
        ];

        // Éves szervizelési statisztikák
        $statisticsYear = [
            'total_cost'    => Service::year($now)->sum('cost'),
            'service_count' => Service::year($now)->count(),
        ];

        // Adatok átadása a frontend számára
        return Inertia::render('Statistics', [
            'fuelMonth'         => $fuelMonth,
            'fuelYear'          => $fuelYear,
            'statisticsMonth'   => $statisticsMonth,
            'statisticsYear'    => $statisticsYear,
            'carDatas'          => Car::all(),
            'periodicMaintenances'  => $periodicMaintenances,
        ]);
    }

    /**
     * Autóra szűrt statisztikák megjelenítése.
     * 
     * Kiszámítja egy adott autó (car_id alapján) statisztikáit a jelenlegi hónapra és évre.
     * Tartalmazza az üzemanyag-fogyasztást, átlagfogyasztást, szervizelési adatokat,
     * valamint a karbantartási emlékeztetőket (olajcsere dátum/km, fékolaj csere, műszaki vizsga).
     * A Fuel és Service modellek month() és year() scope-jaival szűri az adatokat.
     * 
     * @param  \Illuminate\Http\Request  $request A car_id paramétert tartalmazó kérés
     * @return \Inertia\Response Inertia válasz a Statistics nézettel és szűrt statisztikai adatokkal
     */
    public function filteredStatistic(Request $request) {
        // Aktuális dátum lekérése
        $now = Carbon::now();

        // Kiválasztott autó adatainak betöltése
        $carDatas = Car::find($request->car_id);

        // Havi összesítések számításához (adott autóra szűrve)
        $sumMonthKm = Fuel::month($now)->where('car_id', $request->car_id)->sum('km');
        $sumMonthFuel = Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity');

        // Éves összesítések számításához (adott autóra szűrve)
        $sumYearKm = Fuel::year($now)->where('car_id', $request->car_id)->sum('km');
        $sumYearFuel = Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity');

        // Következő olajcsere km-óra állásának kiszámítása
        $next_oil_change_km = $carDatas->last_oil_change_km + $carDatas->oil_change_cycle_km;

        // Karbantartási emlékeztetők kiszámítása a kiválasztott autóra
        $periodicMaintenances = [
            'next_oil_change_date' => Carbon::parse($carDatas->last_oil_change_date)
                ->addYears($carDatas->oil_change_cycle_year)
                ->toDateString(),
            'next_oil_change_km' => $next_oil_change_km,
            'oil_change_km_left' => $next_oil_change_km - $carDatas->current_km,
            'next_break_oil_change_date' => Carbon::parse($carDatas->last_break_oil_change_date)
                ->addYears($carDatas->break_oil_cycle_year)
                ->toDateString(),
            'inspection_valid_until' => $carDatas->inspection_valid_until,
        ];

        // Havi üzemanyag statisztikák összegyűjtése (adott autóra szűrve)
        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::month($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::month($now)->where('car_id', $request->car_id)->sum('money'),
            'monthly_fuel_count'=> Fuel::month($now)->where('car_id', $request->car_id)->count(),
        ];

        // Havi átlagfogyasztás számítása (l/100km), nullázás ha nincs adat
        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        // Éves üzemanyag statisztikák összegyűjtése (adott autóra szűrve)
        $fuelYear = [
            'total_liter'       => Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::year($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::year($now)->where('car_id', $request->car_id)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->where('car_id', $request->car_id)->count(),
        ];

        // Éves átlagfogyasztás számítása (l/100km), nullázás ha nincs adat
        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        // Havi szervizelési statisztikák (adott autóra szűrve)
        $statisticsMonth = [
            'total_cost'    => Service::month($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::month($now)->where('car_id', $request->car_id)->count(),
        ];

        // Éves szervizelési statisztikák (adott autóra szűrve)
        $statisticsYear = [
            'total_cost'    => Service::year($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::year($now)->where('car_id', $request->car_id)->count(),
        ];

        // Adatok átadása a frontend számára
        return Inertia::render('Statistics', [
            'fuelMonth'             => $fuelMonth,
            'fuelYear'              => $fuelYear,
            'statisticsMonth'       => $statisticsMonth,
            'statisticsYear'        => $statisticsYear,
            'carDatas'              => Car::all(),
            'periodicMaintenances'  => $periodicMaintenances,
        ]);
    }
}
