<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/prototype', function () {
    return Inertia::render('Prototype');
})->name('prototype');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'conductor' => redirect()->route('conductor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('pasajero.dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:pasajero')->group(function () {
        Route::get('/dashboard/home', function () {
            return Inertia::render('pasajero/Dashboard');
        })->name('pasajero.dashboard');

        Route::get('/dashboard/reservas', function () {
            return Inertia::render('pasajero/Reservas');
        })->name('pasajero.reservas');

        Route::get('/dashboard/perfil', function () {
            return Inertia::render('pasajero/Perfil');
        })->name('pasajero.perfil');

        Route::get('/dashboard/cartera', function () {
            return Inertia::render('pasajero/Wallet');
        })->name('pasajero.wallet');

        Route::get('/dashboard/viajes', function () {
            return Inertia::render('pasajero/Dashboard');
        })->name('pasajero.viajes');
    });

    Route::middleware('role:conductor')->group(function () {
        Route::get('/conductor/dashboard', function () {
            return Inertia::render('conductor/Dashboard');
        })->name('conductor.dashboard');

        Route::get('/conductor/viajes', function () {
            return Inertia::render('conductor/Dashboard');
        })->name('conductor.viajes');

        Route::get('/conductor/ganancias', function () {
            return Inertia::render('conductor/Dashboard');
        })->name('conductor.earnings');
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

