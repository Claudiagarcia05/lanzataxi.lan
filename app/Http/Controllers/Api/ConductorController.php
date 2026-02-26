<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conductor;
use Illuminate\Http\Request;

class ConductorController extends Controller
{
    public function profile(Request $solicitud)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor) {
            return response()->json(['message' => 'conductor profile not found'], 404);
        }

        // Incluir avatar en la relación user y taxi
        return response()->json($conductor->load(['user:id,name,email,phone,avatar', 'taxi']));
    }

    public function index()
    {
        return response()->json(
            conductor::with(['user:id,name,email,phone', 'taxi:id,conductor_id,plate,model,status'])
                ->latest()
                ->get()
        );
    }

    public function store(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|unique:conductors,license_number',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'nullable|boolean',
        ]);

        $conductor = conductor::create($validated);

        return response()->json($conductor, 201);
    }

    public function show(conductor $conductor)
    {
        return response()->json($conductor->load(['user:id,name,email,phone', 'taxi']));
    }

    public function update(Request $solicitud, conductor $conductor)
    {
        $validated = $solicitud->validate([
            'license_number' => 'sometimes|string|unique:conductors,license_number,' . $conductor->id,
            'rating' => 'sometimes|numeric|min:0|max:5',
            'is_active' => 'sometimes|boolean',
        ]);

        $conductor->update($validated);

        return response()->json($conductor);
    }

    public function destroy(conductor $conductor)
    {
        $conductor->delete();

        return response()->json(['message' => 'conductor deleted']);
    }

    public function status(Request $solicitud)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor) {
            return response()->json(['message' => 'conductor profile not found'], 404);
        }

        return response()->json([
            'is_active' => (bool) $conductor->is_active,
            'rating' => $conductor->rating,
            'taxi' => $conductor->taxi,
        ]);
    }

    public function updateStatus(Request $solicitud)
    {
        $conductor = $solicitud->user()->conductor;

        if (!$conductor) {
            return response()->json(['message' => 'conductor profile not found'], 404);
        }

        $validated = $solicitud->validate([
            'is_active' => 'required|boolean',
        ]);

        $conductor->update(['is_active' => $validated['is_active']]);

        return response()->json($conductor);
    }

    public function nearbyconductors(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $radius = $validated['radius'] ?? 5; // Default 5 km

        try {
            // Por ahora devolvemos conductors activos
            // En producción, implementar cálculo de distancia real con geolocalización
            $conductors = conductor::with(['user:id,name', 'taxi:id,conductor_id,plate,model,status'])
                ->where('is_active', true)
                ->whereHas('user') // Asegurar que tenga usuario
                ->whereHas('taxi', function ($query) {
                    $query->where('status', 'available');
                })
                ->limit(10)
                ->get()
                ->filter(function ($conductor) {
                    // Filtrar conductores que tengan user y taxi válidos
                    return $conductor->user && $conductor->taxi;
                })
                ->map(function ($conductor) {
                    return [
                        'id' => $conductor->id,
                        'conductor_name' => $conductor->user?->name ?? 'Sin nombre',
                        'taxi_model' => $conductor->taxi?->model ?? 'N/A',
                        'taxi_plate' => $conductor->taxi?->plate ?? 'N/A',
                        'rating' => $conductor->rating ?? 4.5,
                        'distance' => round(rand(5, 50) / 10, 1), // Simulado para demo
                    ];
                })
                ->values(); // Re-indexar array después del filtro

            return response()->json($conductors);
        } catch (\Exception $e) {
            \Log::error('Error en nearbyconductors: ' . $e->getMessage());
            
            // Retornar array vacÃƒÂ­o en caso de error
            return response()->json([]);
        }
    }
}

