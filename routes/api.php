<?php

use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TaxiController;
use App\Http\Controllers\Api\TripController;
use Illuminate\Support\Facades\Route;

// Rutas publicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/available-taxis', [TaxiController::class, 'available']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Pasajeros
    Route::get('/user/trips', [TripController::class, 'userTrips']);
    Route::post('/trips', [TripController::class, 'store']);
    Route::get('/trips/{trip}', [TripController::class, 'show']);
    Route::get('/trips/{trip}/track', [TripController::class, 'track']);
    Route::patch('/trips/{trip}/cancel', [TripController::class, 'cancel']);

    // Taxistas
    Route::middleware('role:driver')->group(function () {
        Route::get('/driver/trips', [TripController::class, 'driverTrips']);
        Route::patch('/trips/{trip}/accept', [TripController::class, 'accept']);
        Route::patch('/trips/{trip}/start', [TripController::class, 'start']);
        Route::patch('/trips/{trip}/complete', [TripController::class, 'complete']);
        Route::post('/driver/location', [LocationController::class, 'update']);
        Route::get('/driver/status', [DriverController::class, 'status']);
        Route::patch('/driver/status', [DriverController::class, 'updateStatus']);
    });

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('drivers', DriverController::class);
        Route::apiResource('taxis', TaxiController::class);
        Route::get('/reports/trips', [TripController::class, 'reports']);
    });

    // Pagos
    Route::post('/trips/{trip}/payment', [PaymentController::class, 'store']);
});
