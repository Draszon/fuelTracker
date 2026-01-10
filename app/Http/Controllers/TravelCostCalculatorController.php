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
    public function index () {
        $costs = CostRate::all();
        $user = Auth::user();
        $isAdmin = (bool)($user->is_admin ?? false);
        $now = Carbon::now();

        $baseQuery = TravelCost::with(['car', 'car.user'])
            ->when(!$isAdmin, function ($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            });

        $monthlyKm = (clone $baseQuery)
            ->whereMonth('date', $now->month)
            ->pluck('distance')
            ->sum();
        
        $travelDatas = (clone $baseQuery)->orderBy('date', 'desc')->get();

        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        $amortizationCost = $monthlyKm * $costs[0]->amortization_price;

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

    public function filteredDatas(Request $request) {
        $params = $request->input('params');
        $costs = CostRate::all();
        $user = Auth::user();
        $is_admin = (bool)($user->is_admin ?? false);
        $now = Carbon::now();

        $baseQuery = TravelCost::with('car', 'car.user')
            ->when(!$is_admin, function($query) {
                $query->whereHas('car', fn($car) => $car->where('user_id', Auth::id()));
            });

        $travelDatas = (clone $baseQuery)->orderBy('date', 'desc')->get();

        $carDatas = Car::with('user')
            ->when(!Auth::user()->is_admin, function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        if ($params['month'] && $params['car']) {
            $selectedMonthKm = (clone $baseQuery)
                ->whereMonth('date', $params['month'])
                ->where('car_id', $params['car'])
                ->pluck('distance')
                ->sum();
            
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

    public function storeTravelData (Request $request) {
        $validated = $request->validate([
            'car_id'    => 'required',
            'date'      => 'required|date',
            'direction' => 'required|string',
            'distance'  => 'required|numeric',
        ]);

        try {
            DB::transaction(function() use($validated, $request) {
                $car = Car::findOrFail($request->car_id);
                if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult ehhez a művelethet!');
                }

                $fuelCost = CostRate::where('id', '1')
                    ->value('fuel_price');
                $carConsumption = Car::where('id', $validated['car_id'])
                    ->value('average_fuel_consumption');

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

    public function destroyTravelData ($id) {
        DB::transaction(function () use($id) {
            $selectedToDelete = TravelCost::findOrFail($id);

            if ($selectedToDelete->car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                abort(403, 'Nem vagy jogosult ehhez a művelethez!');
            }

            $selectedToDelete->delete();

            return back()->with('message', 'Sikere adattörlés!');
        });
    }

    public function updateTravelData (Request $request, $id) {
        $validated = $request->validate([
            'car_id'    => 'required',
            'date'      => 'required|date',
            'direction' => 'required|string',
            'distance'  => 'required|numeric',
        ]);

        try {
            DB::transaction(function() use($validated, $request, $id) {
                $travelData = TravelCost::findOrFail($id);

                $car = Car::findOrFail($request->car_id);
                if ($car->user_id !== Auth::id() && !Auth::user()->is_admin) {
                    abort(403, 'Nem vagy jogosult ehhez a művelethet!');
                }

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
