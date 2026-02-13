<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found'], 404);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $location = Location::create([
            'driver_id' => $driver->id,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
        ]);

        return response()->json($location, 201);
    }
}
