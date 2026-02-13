<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function userTrips(Request $request)
    {
        $trips = $request->user()->tripsAsPassenger()->latest()->get();

        return response()->json($trips);
    }

    public function driverTrips(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found'], 404);
        }

        $trips = $driver->trips()->latest()->get();

        return response()->json($trips);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
        ]);

        $trip = Trip::create([
            'passenger_id' => $request->user()->id,
            'pickup_lat' => $validated['pickup_lat'],
            'pickup_lng' => $validated['pickup_lng'],
            'dropoff_lat' => $validated['dropoff_lat'],
            'dropoff_lng' => $validated['dropoff_lng'],
            'status' => 'pending',
        ]);

        $trip->distance = 5.5;
        $trip->price = $trip->distance * 1.2;
        $trip->co2_saved = $trip->calculateCO2Saved();
        $trip->save();

        return response()->json($trip, 201);
    }

    public function show(Trip $trip)
    {
        return response()->json($trip);
    }

    public function cancel(Trip $trip)
    {
        if (!in_array($trip->status, ['pending', 'accepted'], true)) {
            return response()->json(['message' => 'Trip cannot be cancelled'], 400);
        }

        $trip->update(['status' => 'cancelled']);

        return response()->json($trip);
    }

    public function track(Trip $trip)
    {
        if (!$trip->driver) {
            return response()->json(['message' => 'Driver not assigned'], 404);
        }

        $location = $trip->driver->locations()->latest()->first();

        if (!$location) {
            return response()->json(['message' => 'Location not available'], 404);
        }

        return response()->json([
            'location' => [
                'lat' => (float) $location->lat,
                'lng' => (float) $location->lng,
                'updated_at' => $location->updated_at,
            ],
        ]);
    }

    public function accept(Request $request, Trip $trip)
    {
        if ($trip->status !== 'pending') {
            return response()->json(['message' => 'Trip not available'], 400);
        }

        $driver = $request->user()->driver;

        if (!$driver || !$driver->taxi) {
            return response()->json(['message' => 'Driver or taxi not available'], 400);
        }

        $trip->update([
            'driver_id' => $driver->id,
            'taxi_id' => $driver->taxi->id,
            'status' => 'accepted',
        ]);

        return response()->json($trip);
    }

    public function start(Trip $trip)
    {
        if ($trip->status !== 'accepted') {
            return response()->json(['message' => 'Trip cannot be started'], 400);
        }

        $trip->update(['status' => 'in_progress']);

        return response()->json($trip);
    }

    public function complete(Trip $trip)
    {
        if ($trip->status !== 'in_progress') {
            return response()->json(['message' => 'Trip cannot be completed'], 400);
        }

        $trip->update(['status' => 'completed']);

        return response()->json($trip);
    }

    public function reports()
    {
        $data = [
            'total' => Trip::count(),
            'completed' => Trip::where('status', 'completed')->count(),
            'cancelled' => Trip::where('status', 'cancelled')->count(),
            'revenue' => Trip::where('status', 'completed')->sum('price'),
        ];

        return response()->json($data);
    }
}
