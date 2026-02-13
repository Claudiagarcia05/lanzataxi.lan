<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(Driver::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|unique:drivers,license_number',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'nullable|boolean',
        ]);

        $driver = Driver::create($validated);

        return response()->json($driver, 201);
    }

    public function show(Driver $driver)
    {
        return response()->json($driver);
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'license_number' => 'sometimes|string|unique:drivers,license_number,' . $driver->id,
            'rating' => 'sometimes|numeric|min:0|max:5',
            'is_active' => 'sometimes|boolean',
        ]);

        $driver->update($validated);

        return response()->json($driver);
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return response()->json(['message' => 'Driver deleted']);
    }

    public function status(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found'], 404);
        }

        return response()->json([
            'is_active' => (bool) $driver->is_active,
            'rating' => $driver->rating,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found'], 404);
        }

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $driver->update(['is_active' => $validated['is_active']]);

        return response()->json($driver);
    }
}
