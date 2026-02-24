<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conductor;
use App\Models\Taxi;
use App\Models\Viaje;
use App\Models\User;

class AdminController extends Controller
{
    public function users()
    {
        $usuarios = User::query()
            ->latest()
            ->get(['id', 'name', 'email', 'role', 'phone', 'created_at']);

        return response()->json($usuarios);
    }

    public function viajes()
    {
        $viajes = viaje::with(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate'])
            ->latest()
            ->get();

        return response()->json($viajes);
    }

    public function stats()
    {
        $today = now()->toDateString();

        return response()->json([
            'totalUsers' => User::count(),
            'activeconductors' => conductor::where('is_active', true)->count(),
            'totalTaxis' => Taxi::count(),
            'todayTrips' => viaje::whereDate('created_at', $today)->count(),
            'todayRevenue' => (float) viaje::whereDate('created_at', $today)->where('status', 'completed')->sum('price'),
            'averageRating' => round((float) conductor::avg('rating'), 2),
            'weeklyRevenue' => (float) viaje::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'completed')->sum('price'),
            'monthlyRevenue' => (float) viaje::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->where('status', 'completed')->sum('price'),
        ]);
    }

    public function pendingconductors()
    {
        $pendiente = conductor::with(['user:id,name,email,phone', 'taxi:id,conductor_id,plate'])
            ->where('is_active', false)
            ->latest()
            ->get();

        return response()->json($pendiente);
    }
}

