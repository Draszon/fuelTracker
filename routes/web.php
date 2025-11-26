<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\FuelController;
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

    Route::get('/car-tracker', [CarController::class, 'index'])->name('get.Cardata');
});
