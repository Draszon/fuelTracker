<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index() {
        $serviceDatas = Service::with('car')->get();
        $carDatas = Car::all();
        return Inertia::render('ServiceTracker', [
            'carDatas' => $carDatas,
            'serviceDatas' => $serviceDatas,
        ]);
    }
}
