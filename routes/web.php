<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthSessionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Inicio', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/prototype', function () {
    return Inertia::render('Prototype');
})->name('prototype');

Route::get('/auth/session-login', [AuthSessionController::class, 'establishSession'])
    ->name('auth.session-login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'conductor' => redirect()->route('conductor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('pasajero.dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:pasajero')->group(function () {
        Route::get('/pasajero/home', function () {
            return Inertia::render('Pasajero/Panel');
        })->name('pasajero.dashboard');

        Route::get('/pasajero/reservas', function () {
            return Inertia::render('Pasajero/Reservas');
        })->name('pasajero.reservas');

        Route::get('/pasajero/perfil', function () {
            return Inertia::render('Pasajero/Perfil');
        })->name('pasajero.perfil');

        Route::get('/dashboard/cartera', function () {
            return Inertia::render('Pasajero/Cartera');
        })->name('pasajero.wallet');

        Route::get('/dashboard/viajes', function () {
            return Inertia::render('Pasajero/Panel');
        })->name('pasajero.viajes');
    });

    Route::middleware('role:conductor')->group(function () {
        Route::get('/conductor/dashboard', function () {
            return Inertia::render('Conductor/Panel');
        })->name('conductor.dashboard');

        Route::get('/conductor/viajes', function () {
            return Inertia::render('Conductor/Panel');
        })->name('conductor.viajes');

        Route::get('/conductor/ganancias', function () {
            return Inertia::render('Conductor/Panel');
        })->name('conductor.earnings');

        // Ruta para el perfil del conductor
        Route::get('/conductor/perfil', function () {
            return Inertia::render('Conductor/Perfil');
        })->name('conductor.perfil');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        Route::get('/admin/viajes', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.viajes');

        Route::get('/admin/users', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.users');

        Route::get('/admin/taxis', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.taxis');

        Route::get('/admin/reports', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.reports');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

// Catch-all para SPA (Vue Router). Debe ir al final del archivo.
Route::get('/{any}', function () {
    return Inertia::render('Inicio'); // Cambia 'Inicio' si tu vista principal es otra
})->where('any', '.*');

