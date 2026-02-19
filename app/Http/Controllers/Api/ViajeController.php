<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\viaje;
use Illuminate\Http\Request;

class ViajeController extends Controller
{
    public function userviajes(Request $solicitud)
    {
        $viajes = $solicitud->user()->viajesAspasajero()
            ->with(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago'])
            ->latest()
            ->get();

        return response()->json($viajes);
    }

    public function driverTrips(Request $solicitud)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor) {
            return response()->json(['message' => 'conductor profile not found'], 404);
        }

        $viajes = $conductor->viajes()
            ->with(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago'])
            ->latest()
            ->get();

        return response()->json($viajes);
    }

    public function store(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'distance' => 'nullable|numeric|min:0',
            'scheduled_for' => 'nullable|date',
            'pasajeros' => 'nullable|integer|min:1|max:4',
            'luggage' => 'nullable|integer|min:0|max:10',
            'pago_method' => 'nullable|string|in:cash,card,app',
            'notes' => 'nullable|string|max:1000',
        ]);

        $viaje = viaje::create([
            'pasajero_id' => $solicitud->user()->id,
            'pickup_lat' => $validated['pickup_lat'],
            'pickup_lng' => $validated['pickup_lng'],
            'dropoff_lat' => $validated['dropoff_lat'],
            'dropoff_lng' => $validated['dropoff_lng'],
            'pickup_address' => $validated['pickup_address'] ?? null,
            'dropoff_address' => $validated['dropoff_address'] ?? null,
            'scheduled_for' => $validated['scheduled_for'] ?? null,
            'pasajeros' => $validated['pasajeros'] ?? 1,
            'luggage' => $validated['luggage'] ?? 0,
            'pago_method' => $validated['pago_method'] ?? 'cash',
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        $viaje->distance = $validated['distance'] ?? 5.5;
        $viaje->price = $viaje->distance * 1.2;
        $viaje->co2_saved = $viaje->calculateCO2Saved();
        $viaje->save();

        return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']), 201);
    }

    public function show(viaje $viaje)
    {
        return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }

    public function cancel(viaje $viaje)
    {
        if ($viaje->pasajero_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if (!in_array($viaje->status, ['pending', 'accepted'], true)) {
            return response()->json(['message' => 'viaje cannot be cancelled'], 400);
        }

        $viaje->update(['status' => 'cancelled']);

        return response()->json($viaje);
    }

    public function track(viaje $viaje)
    {
        if (!$viaje->conductor) {
            return response()->json(['message' => 'conductor not assigned'], 404);
        }

        $ubicacion = $viaje->conductor->ubicacions()->latest()->first();

        if (!$ubicacion) {
            return response()->json(['message' => 'ubicacion not available'], 404);
        }

        return response()->json([
            'ubicacion' => [
                'lat' => (float) $ubicacion->lat,
                'lng' => (float) $ubicacion->lng,
                'updated_at' => $ubicacion->updated_at,
            ],
        ]);
    }

    public function accept(Request $solicitud, viaje $viaje)
    {
        if ($viaje->status !== 'pending') {
            return response()->json(['message' => 'viaje not available'], 400);
        }

        $conductor = $solicitud->user()->conductor;

        if (!$conductor || !$conductor->taxi) {
            return response()->json(['message' => 'conductor or taxi not available'], 400);
        }

        $viaje->update([
            'conductor_id' => $conductor->id,
            'taxi_id' => $conductor->taxi->id,
            'status' => 'accepted',
        ]);

        return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }

    public function start(Request $solicitud, viaje $viaje)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor || (int) $viaje->conductor_id !== (int) $conductor->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($viaje->status !== 'accepted') {
            return response()->json(['message' => 'viaje cannot be started'], 400);
        }

        $viaje->update(['status' => 'in_progress']);

        return response()->json($viaje->fresh(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }

    public function complete(Request $solicitud, viaje $viaje)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor || (int) $viaje->conductor_id !== (int) $conductor->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($viaje->status !== 'in_progress') {
            return response()->json(['message' => 'viaje cannot be completed'], 400);
        }

        $validated = $solicitud->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $viaje->update([
            'status' => 'completed',
            'rating' => $validated['rating'] ?? $viaje->rating,
            'comment' => $validated['comment'] ?? $viaje->comment,
            'end_time' => now(),
        ]);

        return response()->json($viaje->fresh(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }

    public function reports()
    {
        $data = [
            'total' => viaje::count(),
            'completed' => viaje::where('status', 'completed')->count(),
            'cancelled' => viaje::where('status', 'cancelled')->count(),
            'revenue' => viaje::where('status', 'completed')->sum('price'),
        ];

        return response()->json($data);
    }

    public function rate(Request $solicitud, viaje $viaje)
    {
        if ($viaje->pasajero_id !== $solicitud->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($viaje->status !== 'completed') {
            return response()->json(['message' => 'Solo puedes valorar viajes completados'], 400);
        }

        $validated = $solicitud->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $viaje->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json($viaje->fresh(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }
}

