<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Fuel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index() {
        //a főoldalon megjelenő adatok lekérdezése és szűrése
        //alapértelmezetten: kocsi, üzemanyag, szerviz, biztosítás
        $cars = Car::all();

        $now = Carbon::now();

        $fuelMoneyMonthSum = Fuel::whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->sum('money');

        return Inertia::render('Statistics', [
            'cars' => $cars,
            'fuelMoneyMonthSum' => $fuelMoneyMonthSum,
        ]);
    }
}
