<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index() {
        $serviceDatas = Service::with('car')->orderBy('date', 'desc')->get();
        $carDatas = Car::all();
        return Inertia::render('ServiceTracker', [
            'carDatas' => $carDatas,
            'serviceDatas' => $serviceDatas,
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'car_id' => 'required',
            'date' => 'required|date',
            'current_km' => 'required|numeric',
            'description' => 'required|string',
            'cost' => 'required|numeric',
        ]);

        try {
            Service::create($validated);
            return redirect()->back()->with('message', 'Sikeres adatfeltöltés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok feltöltése közben: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        $selectedServiceData = Service::findOrFail($id);
        $selectedServiceData->delete();
        return redirect()->back()->with('message', 'Sikeres törlés!');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'car_id' => 'required',
            'date' => 'required|date',
            'current_km' => 'required|numeric',
            'description' => 'required|string',
            'cost' => 'required|numeric',
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
