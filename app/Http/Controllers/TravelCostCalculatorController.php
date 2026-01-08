<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CostRate;
use App\Models\TravelCost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use DB;

class TravelCostCalculatorController extends Controller
{
    public function index () {
        $costs = CostRate::all();
        $travelDatas = TravelCost::with(['car', 'car.user'])
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            })
            ->orderBy('date', 'desc')
            ->get();
            
        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        return Inertia::render('TravelCostCalculator', [
            'costs'         => $costs,
            'travelDatas'   => $travelDatas,
            'carDatas'      => $carDatas,
        ]);
    }

    public function storeTravelData (Request $request) {
        $validated = $request->validate([
            'car_id'    => 'required|numeric',
            'date'      => 'required|date',
            'direction' => 'required|string',
            'distance'  => 'required|numeric',
        ]);

        try {
            DB::transaction(function() use($validated, $request) {
                $fuelCost = CostRate::find(1);
                dd($fuelCost);
            });
        } catch (\Exception $e) {
            return redirect()->with('message', 'Hiba történt az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    public function updateFuelPrice (Request $request) {
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

    public function updateAmortizationPrice (Request $request) {
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
}
