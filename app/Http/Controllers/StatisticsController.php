<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    //local scope segítségével hónap és év szerint a modellben leszűrt
    //adatokat megkapja és összegzi vagy átlagolja, majd
    //átadja props ként a nézetnek
    public function index() {
        $now = Carbon::now();
        $sumMonthKm = Fuel::month($now)->sum('km');
        $sumMonthFuel = Fuel::month($now)->sum('quantity');
        $sumYearKm = Fuel::year($now)->sum('km');
        $sumYearFuel = Fuel::year($now)->sum('quantity');

        $periodicMaintenances = [
            'next_oil_change_date' => 0,
            'next_oil_change_km' => 0,
            'oil_change_km_left' => 0,
            'next_break_oil_change_date' => 0,
            'inspection_valid_until' => 0,
        ];

        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->sum('quantity'),
            'total_km'          => Fuel::month($now)->sum('km'),
            'total_cost'        => Fuel::month($now)->sum('money'),
            'monthly_fuel_count'=> Fuel::month($now)->count(),
        ];

        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        $fuelYear = [
            'total_liter'       => Fuel::year($now)->sum('quantity'),
            'total_km'          => Fuel::year($now)->sum('km'),
            'total_cost'        => Fuel::year($now)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->count(),
        ];

        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        $statisticsMonth = [
            'total_cost'    => Service::month($now)->sum('cost'),
            'service_count' => Service::month($now)->count(),
        ];

        $statisticsYear = [
            'total_cost'    => Service::year($now)->sum('cost'),
            'service_count' => Service::year($now)->count(),
        ];

        return Inertia::render('Statistics', [
            'fuelMonth'         => $fuelMonth,
            'fuelYear'          => $fuelYear,
            'statisticsMonth'   => $statisticsMonth,
            'statisticsYear'    => $statisticsYear,
            'carDatas'          => Car::all(),
            'periodicMaintenances'  => $periodicMaintenances,
        ]);
    }

    public function filteredStatistic(Request $request) {
        $now = Carbon::now();
        $carDatas = Car::find($request->car_id);
        $sumMonthKm = Fuel::month($now)->where('car_id', $request->car_id)->sum('km');
        $sumMonthFuel = Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity');
        $sumYearKm = Fuel::year($now)->where('car_id', $request->car_id)->sum('km');
        $sumYearFuel = Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity');
        $next_oil_change_km = $carDatas->last_oil_change_km + $carDatas->oil_change_cycle_km;

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

        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::month($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::month($now)->where('car_id', $request->car_id)->sum('money'),
            'monthly_fuel_count'=> Fuel::month($now)->where('car_id', $request->car_id)->count(),
        ];

        if($sumMonthKm == 0 || $sumMonthFuel == 0) {
            $fuelMonth['avg_consumption'] = 0;
        } else {
            $fuelMonth['avg_consumption'] = round((($sumMonthFuel / $sumMonthKm) * 100) ?? 0 , 1);
        }

        $fuelYear = [
            'total_liter'       => Fuel::year($now)->where('car_id', $request->car_id)->sum('quantity'),
            'total_km'          => Fuel::year($now)->where('car_id', $request->car_id)->sum('km'),
            'total_cost'        => Fuel::year($now)->where('car_id', $request->car_id)->sum('money'),
            'yearly_fuel_count' => Fuel::year($now)->where('car_id', $request->car_id)->count(),
        ];

        if($sumYearKm == 0 || $sumYearFuel == 0) {
            $fuelYear['avg_consumption'] = 0;
        } else {
            $fuelYear['avg_consumption'] = round((($sumYearFuel / $sumYearKm) * 100) ?? 0 , 1);
        }

        $statisticsMonth = [
            'total_cost'    => Service::month($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::month($now)->where('car_id', $request->car_id)->count(),
        ];

        $statisticsYear = [
            'total_cost'    => Service::year($now)->where('car_id', $request->car_id)->sum('cost'),
            'service_count' => Service::year($now)->where('car_id', $request->car_id)->count(),
        ];

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
