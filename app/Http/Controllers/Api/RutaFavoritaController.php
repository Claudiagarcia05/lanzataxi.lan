<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RutaFavorita;
use Illuminate\Http\Request;

class RutaFavoritaController extends Controller
{
    public function index(Request $solicitud)
    {
        $favorites = $solicitud->user()->RutaFavoritas()
            ->orderBy('order')
            ->get();

        return response()->json($favorites);
    }

    public function store(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $maxOrder = $solicitud->user()->RutaFavoritas()->max('order') ?? -1;

        $favorite = $solicitud->user()->RutaFavoritas()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return response()->json($favorite, 201);
    }

    public function update(Request $solicitud, RutaFavorita $favorite)
    {
        if ($favorite->user_id !== $solicitud->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $solicitud->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
            'order' => 'sometimes|integer|min:0',
        ]);

        $favorite->update($validated);

        return response()->json($favorite);
    }

    public function destroy(RutaFavorita $favorite)
    {
        if ($favorite->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $favorite->delete();

        return response()->json(['message' => 'Ruta favorita eliminada']);
    }

    public function reorder(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'favorites' => 'required|array',
            'favorites.*.id' => 'required|exists:rutas_favoritas,id',
            'favorites.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['favorites'] as $item) {
            RutaFavorita::where('id', $item['id'])
                ->where('user_id', $solicitud->user()->id)
                ->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Orden actualizado']);
    }
}

