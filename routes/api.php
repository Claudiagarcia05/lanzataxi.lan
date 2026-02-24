<?php

use App\Http\Controllers\Api\ConductorController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\TaxiController;
use App\Http\Controllers\Api\ViajeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RutaFavoritaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/available-taxis', [TaxiController::class, 'available']);
Route::get('/nearby-conductors', [ConductorController::class, 'nearbyconductors']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Perfil de usuario
    Route::post('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::put('/user/preferences', [UserController::class, 'updatePreferences']);
    Route::delete('/user', [UserController::class, 'deleteAccount']);

    // Cartera virtual
    Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
    Route::get('/wallet/debts', [WalletController::class, 'getDebtSummary']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);
    Route::post('/wallet/add', [WalletController::class, 'addFunds']);
    Route::post('/wallet/use', [WalletController::class, 'useFunds']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdrawFunds']);

    // Rutas favoritas
    Route::get('/favorites', [RutaFavoritaController::class, 'index']);
    Route::post('/favorites', [RutaFavoritaController::class, 'store']);
    Route::put('/favorites/{favorite}', [RutaFavoritaController::class, 'update']);
    Route::delete('/favorites/{favorite}', [RutaFavoritaController::class, 'destroy']);
    Route::post('/favorites/reorder', [RutaFavoritaController::class, 'reorder']);

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    // Pasajeros
    Route::get('/user/viajes', [ViajeController::class, 'userviajes']);
    Route::post('/viajes', [ViajeController::class, 'store']);
    Route::get('/viajes/{viaje}', [ViajeController::class, 'show']);
    Route::get('/viajes/{viaje}/track', [ViajeController::class, 'track']);
    Route::patch('/viajes/{viaje}/cancel', [ViajeController::class, 'cancel']);
    Route::patch('/viajes/{viaje}/rate', [ViajeController::class, 'rate']);

    // Taxistas
    // Las siguientes rutas están disponibles para cualquier usuario autenticado (para pruebas)
    Route::get('/conductor/profile', [ConductorController::class, 'profile']);
    Route::get('/conductor/status', [ConductorController::class, 'status']);
    // El resto sigue protegido por role:conductor
    Route::middleware('role:conductor')->group(function () {
        Route::get('/conductor/viajes', [ViajeController::class, 'driverTrips']);
        Route::patch('/viajes/{viaje}/accept', [ViajeController::class, 'accept']);
        Route::patch('/viajes/{viaje}/start', [ViajeController::class, 'start']);
        Route::patch('/viajes/{viaje}/complete', [ViajeController::class, 'complete']);
        Route::post('/conductor/ubicacion', [UbicacionController::class, 'update']);
        Route::patch('/conductor/status', [ConductorController::class, 'updateStatus']);
    });

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::get('/admin/viajes', [AdminController::class, 'viajes']);
        Route::get('/admin/pending-conductors', [AdminController::class, 'pendingconductors']);
        Route::get('/admin/stats', [AdminController::class, 'stats']);

        Route::apiResource('conductors', ConductorController::class);
        Route::apiResource('taxis', TaxiController::class);
        Route::get('/reports/viajes', [ViajeController::class, 'reports']);
    });

    // Pagos
    Route::post('/viajes/{viaje}/pago', [PagoController::class, 'store']);
    Route::get('/viajes/{viaje}/pago', [PagoController::class, 'show']);
    Route::post('/viajes/{viaje}/pago/stripe', [PagoController::class, 'processstripe']);
    Route::post('/viajes/{viaje}/pago/paypal', [PagoController::class, 'processPayPal']);
});

