<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    //felhasználó hozzáadása admin jogosultággal rendelkezőknek
    Route::get('/user/add', [AdminUserController::class, 'index'])
        ->name('add.user')
        ->middleware('can:register-user');
        
    Route::post('/register', [AdminUserController::class, 'create'])->name('register');

    //főoldal (statisztikai oldal) route-ok
    Route::get('/', [StatisticsController::class, 'index'])->name('get.statistics');
    Route::get('/filtered-statistics', [StatisticsController::class, 'filteredStatistic'])->name('filtered.stats');

    //üzemanyag oldal route-ok
    Route::get('/fuel-tracker', [FuelController::class, 'index'])->name('get.fuelData');
    Route::post('/fuel-store', [FuelController::class, 'store'])->name('store.fuelData');
    Route::delete('/fuel-delete/{id}', [FuelController::class, 'destroy'])->name('destroy.fuelData');
    Route::put('/fuel-update/{id}', [FuelController::class, 'update'])->name('update.fuelData');

    //kocsi adatok oldal route-ok
    Route::get('/car-tracker', [CarController::class, 'index'])->name('get.cardata');
    Route::post('/car-store', [CarController::class, 'store'])->name('store.car');
    Route::delete('/car-delete/{id}', [CarController::class, 'destroy'])->name('destroy.car');
    Route::put('/car-update/{id}', [CarController::class, 'update'])->name('update.car');

    //szerviztevékenység oldal route-ok
    Route::get('/service-tracker', [ServiceController::class, 'index'])->name('get.serviceData');
    Route::post('/service-store', [ServiceController::class, 'store'])->name('store.serviceData');
    Route::delete('/service-delete/{id}', [ServiceController::class, 'destroy'])->name('destroy.serviceData');
    Route::put('/service-update/{id}', [ServiceController::class, 'update'])->name('update.serviceData');

    //biztosítás oldal route-ok
    Route::get('/insurance-tracker', [InsuranceController::class, 'index'])->name('get.insuranceData');
    Route::post('/insurance-store', [InsuranceController::class, 'store'])->name('store.insuranceData');
    Route::put('/insurance-update/{id}', [InsuranceController::class, 'update'])->name('update.insuranceData');
    Route::delete('/insurance-delete/{id}', [InsuranceController::class, 'destroy'])->name('destroy.insuranceData');

    //útiköltség kalkulátor route-ok
    
});
