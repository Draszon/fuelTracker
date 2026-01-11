<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\EditUserController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TravelCostCalculatorController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * Hitelesített és ellenőrzött felhasználók által elérhető route-ok.
 * Sanctum authentikáció + email verifikáció szükséges.
 */
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    // Dashboard megjelenítése
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    /**
     * Admin szerepkörrel rendelkező felhasználók számára elérhető route-ok.
     * checkRole middleware ellenőrzi a jogosultságot.
     */
    Route::middleware(['checkRole'])->group(function () {
        // Felhasználók listázása és kezelése
        Route::get('/users', [EditUserController::class, 'index'])->name('users.list');
        Route::put('/user/update/{id}', [EditUserController::class, 'update'])->name('user.edit');
        Route::put('/update/passwd/{id}', [EditUserController::class, 'updatePasswd'])->name('update.passwd');
        Route::delete('/user/delete/{id}', [EditUserController::class, 'destroy'])->name('destroy.user');

        // Új felhasználó hozzáadása form megjelenítése
        Route::get('/user/add', function () {
            return inertia('Admin/Create');
        })->name('add.user');
    });

    // Új felhasználó regisztrálása (admin által)
    Route::post('/register', [AdminUserController::class, 'create'])->name('register');

    /**
     * Főoldal - Statisztikai adatok megjelenítése és szűrése
     */
    Route::get('/', [StatisticsController::class, 'index'])->name('get.statistics');
    Route::get('/filtered-statistics', [StatisticsController::class, 'filteredStatistic'])->name('filtered.stats');

    /**
     * Üzemanyag-fogyasztás követése
     * CRUD műveletek: listázás, hozzáadás, módosítás, törlés
     */
    Route::get('/fuel-tracker', [FuelController::class, 'index'])->name('get.fuelData');
    Route::post('/fuel-store', [FuelController::class, 'store'])->name('store.fuelData');
    Route::delete('/fuel-delete/{id}', [FuelController::class, 'destroy'])->name('destroy.fuelData');
    Route::put('/fuel-update/{id}', [FuelController::class, 'update'])->name('update.fuelData');

    /**
     * Járműadatok kezelése
     * CRUD műveletek: listázás, hozzáadás, módosítás, törlés
     */
    Route::get('/car-tracker', [CarController::class, 'index'])->name('get.cardata');
    Route::post('/car-store', [CarController::class, 'store'])->name('store.car');
    Route::delete('/car-delete/{id}', [CarController::class, 'destroy'])->name('destroy.car');
    Route::put('/car-update/{id}', [CarController::class, 'update'])->name('update.car');

    /**
     * Szerviz tevékenységek nyilvántartása
     * CRUD műveletek: listázás, hozzáadás, módosítás, törlés
     */
    Route::get('/service-tracker', [ServiceController::class, 'index'])->name('get.serviceData');
    Route::post('/service-store', [ServiceController::class, 'store'])->name('store.serviceData');
    Route::delete('/service-delete/{id}', [ServiceController::class, 'destroy'])->name('destroy.serviceData');
    Route::put('/service-update/{id}', [ServiceController::class, 'update'])->name('update.serviceData');

    /**
     * Biztosítási adatok kezelése
     * CRUD műveletek: listázás, hozzáadás, módosítás, törlés
     */
    Route::get('/insurance-tracker', [InsuranceController::class, 'index'])->name('get.insuranceData');
    Route::post('/insurance-store', [InsuranceController::class, 'store'])->name('store.insuranceData');
    Route::put('/insurance-update/{id}', [InsuranceController::class, 'update'])->name('update.insuranceData');
    Route::delete('/insurance-delete/{id}', [InsuranceController::class, 'destroy'])->name('destroy.insuranceData');

    // Útiköltség kalkulátor - még nincs implementálva
    Route::get('/travel-cost-calculator', [TravelCostCalculatorController::class, 'index']);
    Route::put('/update-fuel-price', [TravelCostCalculatorController::class, 'updateFuelPrice'])->name('update.fuelPrice');
    Route::put('/update-amortization', [TravelCostCalculatorController::class, 'updateAmortizationPrice'])->name('update.amortizationPrice');
    Route::post('/store-travel-data', [TravelCostCalculatorController::class, 'storeTravelData'])->name('store.travelData');
    Route::delete('/delete-travel-data/{id}', [TravelCostCalculatorController::class, 'destroyTravelData'])->name('destroy.travelData');
    Route::put('/update-travel-data/{id}', [TravelCostCalculatorController::class, 'updateTravelData'])->name('update.travelData');
    Route::get('/filtered-data', [TravelCostCalculatorController::class, 'filteredDatas'])->name('filtered.datas');
});
