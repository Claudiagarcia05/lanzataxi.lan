<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Debt;
use Illuminate\Http\Request;

class ViajeController extends Controller
{
    /**
     * Calcula el precio del viaje según origen, destino, distancia, hora y tarifas fijas.
     */
    private function calcularPrecio($origen, $destino, $distance, $hora = null)
    {
        // Municipios principales
        $municipios = [
            'Arrecife', 'Puerto del Carmen', 'Costa Teguise', 'Playa Blanca', 'Haria', 'Teguise', 'Aeropuerto', 'Puerto Calero'
        ];

        // Trayectos fijos (día/noche)
        $trayectosFijos = [
            ['Aeropuerto', 'Arrecife', 10, 14],
            ['Aeropuerto', 'Puerto del Carmen', 12, 18],
            ['Aeropuerto', 'Costa Teguise', 20, 24],
            ['Arrecife', 'Playa Blanca', 45, 50],
            ['Puerto Calero', 'Aeropuerto', 45.86, null],
        ];

        // Determinar si es noche (22:00-06:00)
        $isNoche = false;
        if ($hora) {
            $h = is_string($hora) ? date('H', strtotime($hora)) : $hora->format('H');
            $isNoche = ($h >= 22 || $h < 6);
        }

        // Normalizar nombres
        $origen = ucfirst(strtolower($origen));
        $destino = ucfirst(strtolower($destino));

        // Buscar trayecto fijo
        foreach ($trayectosFijos as $t) {
            if ((($t[0] === $origen && $t[1] === $destino) || ($t[1] === $origen && $t[0] === $destino))) {
                if ($isNoche && $t[3]) return $t[3];
                return $t[2];
            }
        }

        // Si es dentro de Arrecife
        if ($origen === 'Arrecife' && $destino === 'Arrecife') {
            $bajada = $isNoche ? 3.65 : 3.05;
            $precioKm = $isNoche ? 0.92 : 0.80;
            return round($bajada + ($distance * $precioKm), 2);
        }

        // Si uno de los dos es Arrecife, aplicar tarifa Arrecife
        if ($origen === 'Arrecife' || $destino === 'Arrecife') {
            $bajada = $isNoche ? 3.65 : 3.05;
            $precioKm = $isNoche ? 0.92 : 0.80;
            return round($bajada + ($distance * $precioKm), 2);
        }

        // Otros municipios
        $bajada = 3.50;
        $precioKm = 1.10;
        return round($bajada + ($distance * $precioKm), 2);
    }
    public function userviajes(Request $solicitud)
    {
        $user = $solicitud->user();
        $viajes = $user->viajesAspasajero()
            ->with([
                'pasajero:id,name,email',
                'conductor.user:id,name',
                'taxi:id,plate,model',
                'pago'
            ])
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
            'scheduled_for' => 'nullable|date_format:Y-m-d H:i',
            'pasajeros' => 'nullable|integer|min:1|max:6',
            'luggage' => 'nullable|integer|min:0|max:10',
            'pago_method' => 'nullable|string|in:efectivo,wallet,tarjeta',
            'notes' => 'nullable|string|max:1000',
        ]);



        // Municipios principales (debe estar definido antes de usarse en $getMunicipio)
        $municipios = [
            'Arrecife', 'Puerto del Carmen', 'Costa Teguise', 'Playa Blanca', 'Haria', 'Teguise', 'Aeropuerto', 'Puerto Calero'
        ];

        // Extraer municipio de origen y destino (simplificado: primer municipio en la dirección)
        $origen = $validated['pickup_address'] ?? 'Arrecife';
        $destino = $validated['dropoff_address'] ?? 'Arrecife';
        $distance = $validated['distance'] ?? 5.5;
        $hora = $validated['scheduled_for'] ?? now();

        // Intentar extraer municipio de la dirección (mejorable con geocoding real)
        $getMunicipio = function($direccion) use ($municipios) {
            foreach ($municipios as $m) {
                if (stripos($direccion, $m) !== false) return $m;
            }
            return 'Arrecife';
        };
        $mun_origen = $getMunicipio($origen);
        $mun_destino = $getMunicipio($destino);

        $precio = $this->calcularPrecio($mun_origen, $mun_destino, $distance, $hora);

        // Validar saldo si método de pago es cartera virtual
        if ($this->mapPaymentMethod($validated['pago_method'] ?? 'efectivo') === 'app') {
            $usuario = $solicitud->user();
            $saldo = $usuario->wallet_balance ?? 0;
            if ($saldo < $precio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente en la cartera virtual. Añade dinero o elige otro método de pago.',
                    'current_balance' => $saldo,
                    'required_amount' => $precio
                ], 400);
            }
        }

        $viaje = Viaje::create([
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
            'pago_method' => $this->mapPaymentMethod($validated['pago_method'] ?? 'efectivo'),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'distance' => $distance,
            'price' => $precio,
        ]);

        $viaje->co2_saved = $viaje->calculateCO2Saved();
        $viaje->save();

        $pendingDebt = Debt::where('user_id', $viaje->pasajero_id)
            ->where('status', 'pending')
            ->sum('amount');

        if ($pendingDebt > 0) {
            $viaje->price += $pendingDebt;
            $viaje->save();

            Debt::where('user_id', $viaje->pasajero_id)
                ->where('status', 'pending')
                ->update(['status' => 'paid']);
        }

        return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']), 201);
    }

    public function show(Viaje $viaje)
    {
        return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
    }

    public function cancel(Viaje $viaje)
    {
        if ($viaje->pasajero_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if (!in_array($viaje->status, ['pending', 'accepted'], true)) {
            return response()->json(['message' => 'viaje cannot be cancelled'], 400);
        }

        $user = auth()->user();
        $tripPrice = $viaje->price ?? 0;
        $currentBalance = $user->wallet_balance ?? 0;

        // Cambiar estado a cancelado
        $viaje->update(['status' => 'cancelled']);

        if ($currentBalance >= $tripPrice) {
            $user->wallet_balance = $currentBalance - $tripPrice;
            $user->save();
        } else {
            $debtAmount = $tripPrice - $currentBalance;
            $user->wallet_balance = 0;
            $user->save();

            Debt::create([
                'user_id' => $user->id,
                'trip_id' => $viaje->id,
                'amount' => $debtAmount,
                'status' => 'pending',
                'reason' => 'Cancelación de viaje - Cobro total'
            ]);
        }

        return response()->json($viaje);
    }

    public function track(Viaje $viaje)
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

    public function accept(Request $solicitud, Viaje $viaje)
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

    public function start(Request $solicitud, Viaje $viaje)
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

    public function complete(Request $solicitud, Viaje $viaje)
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
            'total' => Viaje::count(),
            'completed' => Viaje::where('status', 'completed')->count(),
            'cancelled' => Viaje::where('status', 'cancelled')->count(),
            'revenue' => Viaje::where('status', 'completed')->sum('price'),
        ];

        return response()->json($data);
    }

    public function rate(Request $solicitud, Viaje $viaje)
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

    /**
     * Normaliza los métodos de pago del frontend a los valores de la base de datos
     */
    private function mapPaymentMethod($method)
    {
        $map = [
            'efectivo' => 'cash',
            'wallet' => 'app',
            'tarjeta' => 'card',
            'cash' => 'cash',
            'card' => 'card',
            'app' => 'app',
        ];

        return $map[$method] ?? 'cash';
    }
}

