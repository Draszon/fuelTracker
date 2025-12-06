<?php

namespace App\Http\Controllers;

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

        $fuelMonth = [
            'total_liter'       => Fuel::month($now)->sum('quantity'),
            'total_km'          => Fuel::month($now)->sum('km'),
            'total_cost'        => Fuel::month($now)->sum('money'),
            'avg_consumption'   => round(Fuel::month($now)->avg('consumption') ?? 0 , 1),
            'monthly_fuel_count'=> Fuel::month($now)->count(),
        ];

        $fuelYear = [
            'total_liter'       => Fuel::year($now)->sum('quantity'),
            'total_km'          => Fuel::year($now)->sum('km'),
            'total_cost'        => Fuel::year($now)->sum('money'),
            'avg_consumption'   => round(Fuel::year($now)->avg('consumption') ?? 0 , 1),
            'yearly_fuel_count' => Fuel::year($now)->count(),
        ];

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
        ]);
    }
}
