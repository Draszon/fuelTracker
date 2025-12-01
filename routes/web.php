<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\ServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/', function() {
        return Inertia::render('Statistics');
    })->name('main');

    Route::get('/fuel-tracker', [FuelController::class, 'index'])->name('get.fuelData');
    Route::post('/fuel-store', [FuelController::class, 'store'])->name('store.fuelData');
    Route::delete('/fuel-delete/{id}', [FuelController:: class, 'destroy'])->name('destroy.fuelData');
    Route::put('/fuel-update/{id}', [FuelController::class, 'update'])->name('update.fuelData');

    Route::get('/car-tracker', [CarController::class, 'index'])->name('get.cardata');
    Route::post('/car-store', [CarController::class, 'store'])->name('store.car');
    Route::delete('/car-delete/{id}', [CarController::class, 'destroy'])->name('destroy.car');
    Route::put('/car-update/{id}', [CarController::class, 'update'])->name('update.car');

    Route::get('/service-tracker', [ServiceController::class, 'index'])->name('get.serviceData');
    Route::post('/service-store', [ServiceController::class, 'store'])->name('store.serviceData');
    Route::delete('/service-delete/{id}', [ServiceController::class, 'destroy'])->name('destroy.serviceData');
    Route::put('/service-update/{id}', [ServiceController::class, 'update'])->name('update.serviceData');
});
