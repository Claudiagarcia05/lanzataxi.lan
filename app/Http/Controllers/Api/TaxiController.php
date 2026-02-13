<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taxi;
use Illuminate\Http\Request;

class TaxiController extends Controller
{
    public function index()
    {
        return response()->json(Taxi::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'plate' => 'required|string|unique:taxis,plate',
            'model' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'color' => 'nullable|string',
            'status' => 'nullable|in:available,busy,offline',
        ]);

        $taxi = Taxi::create($validated);

        return response()->json($taxi, 201);
    }

    public function show(Taxi $taxi)
    {
        return response()->json($taxi);
    }

    public function update(Request $request, Taxi $taxi)
    {
        $validated = $request->validate([
            'driver_id' => 'sometimes|exists:drivers,id',
            'plate' => 'sometimes|string|unique:taxis,plate,' . $taxi->id,
            'model' => 'sometimes|string',
            'capacity' => 'sometimes|integer|min:1',
            'color' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:available,busy,offline',
        ]);

        $taxi->update($validated);

        return response()->json($taxi);
    }

    public function destroy(Taxi $taxi)
    {
        $taxi->delete();

        return response()->json(['message' => 'Taxi deleted']);
    }

    public function available()
    {
        $taxis = Taxi::where('status', 'available')->get();

        return response()->json($taxis);
    }
}
