<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function update(Request $solicitud)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor) {
            return response()->json(['message' => 'conductor profile not found'], 404);
        }

        $validated = $solicitud->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $ubicacion = ubicacion::create([
            'conductor_id' => $conductor->id,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
        ]);

        return response()->json($ubicacion, 201);
    }
}

