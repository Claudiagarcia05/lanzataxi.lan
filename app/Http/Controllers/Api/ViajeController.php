<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Viaje;
    use App\Models\Debt;
    use App\Models\Pago;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class ViajeController extends Controller {
        private function calcularPrecio($origen, $destino, $distance, $hora = null) {
            $municipios = [
                'Arrecife', 'Puerto del Carmen', 'Costa Teguise', 'Playa Blanca', 'Haria', 'Teguise', 'Aeropuerto', 'Puerto Calero'
            ];

            $trayectosFijos = [
                ['Aeropuerto', 'Arrecife', 10, 14],
                ['Aeropuerto', 'Puerto del Carmen', 12, 18],
                ['Aeropuerto', 'Costa Teguise', 20, 24],
                ['Arrecife', 'Playa Blanca', 45, 50],
                ['Puerto Calero', 'Aeropuerto', 45.86, null],
            ];

            $isNoche = false;
            if ($hora) {
                $h = is_string($hora) ? date('H', strtotime($hora)) : $hora->format('H');
                $isNoche = ($h >= 22 || $h < 6);
            }

            $origen = ucfirst(strtolower($origen));
            $destino = ucfirst(strtolower($destino));

            foreach ($trayectosFijos as $t) {
                if ((($t[0] === $origen && $t[1] === $destino) || ($t[1] === $origen && $t[0] === $destino))) {
                    if ($isNoche && $t[3]) return $t[3];

                    return $t[2];
                }
            }

            if ($origen === 'Arrecife' && $destino === 'Arrecife') {
                $bajada = $isNoche ? 3.65 : 3.05;
                $precioKm = $isNoche ? 0.92 : 0.80;

                return round($bajada + ($distance * $precioKm), 2);
            }

            if ($origen === 'Arrecife' || $destino === 'Arrecife') {
                $bajada = $isNoche ? 3.65 : 3.05;
                $precioKm = $isNoche ? 0.92 : 0.80;

                return round($bajada + ($distance * $precioKm), 2);
            }

            $bajada = 3.50;
            $precioKm = 1.10;

            return round($bajada + ($distance * $precioKm), 2);
        }
        public function userviajes(Request $solicitud) {
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

        public function driverTrips(Request $solicitud) {
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


        public function available(Request $solicitud) {
            $conductor = $solicitud->user()->conductor;

            if (!$conductor) {

                return response()->json(['message' => 'conductor profile not found'], 404);
            }

            if (!$conductor->is_active) {

                return response()->json([]);
            }

            $viajes = Viaje::query()
                ->where('status', 'pending')
                ->whereNull('conductor_id')
                ->with([
                    'pasajero:id,name',
                    'conductor.user:id,name',
                    'taxi:id,plate,model',
                    'pago',
                ])
                ->latest()
                ->get();

            return response()->json($viajes);
        }

        public function store(Request $solicitud) {
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

            $municipios = [
                'Arrecife', 'Puerto del Carmen', 'Costa Teguise', 'Playa Blanca', 'Haria', 'Teguise', 'Aeropuerto', 'Puerto Calero'
            ];

            $origen = $validated['pickup_address'] ?? 'Arrecife';
            $destino = $validated['dropoff_address'] ?? 'Arrecife';
            $distance = $validated['distance'] ?? 5.5;
            $hora = $validated['scheduled_for'] ?? now();

            $getMunicipio = function($direccion) use ($municipios) {
                foreach ($municipios as $m) {
                    if (stripos($direccion, $m) !== false) return $m;
                }

                return 'Arrecife';
            };
            $mun_origen = $getMunicipio($origen);
            $mun_destino = $getMunicipio($destino);

            $precio = $this->calcularPrecio($mun_origen, $mun_destino, $distance, $hora);

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

        public function show(Viaje $viaje) {

            return response()->json($viaje->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
        }

        public function cancel(Viaje $viaje) {
            if ($viaje->pasajero_id !== auth()->id()) {
                
                return response()->json(['message' => 'No autorizado'], 403);
            }

            if (!in_array($viaje->status, ['pending', 'accepted'], true)) {

                return response()->json(['message' => 'viaje cannot be cancelled'], 400);
            }

            $user = auth()->user();
            $tripPrice = $viaje->price ?? 0;
            $currentBalance = $user->wallet_balance ?? 0;

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

        public function track(Viaje $viaje) {
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

        public function accept(Request $solicitud, Viaje $viaje) {
            $conductor = $solicitud->user()->conductor;

            if (!$conductor) {

                return response()->json(['message' => 'conductor profile not found'], 404);
            }

            if (!$conductor->is_active) {

                return response()->json(['message' => 'conductor not active'], 403);
            }

            try {
                $viajeActualizado = DB::transaction(function () use ($viaje, $conductor) {
                    $locked = Viaje::query()
                        ->whereKey($viaje->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$locked) {
                        return null;
                    }

                    if ($locked->status !== 'pending' || $locked->conductor_id !== null) {
                        return false;
                    }

                    $taxiId = $conductor->taxi?->id;
                    if (!$taxiId) {
                        $taxiId = $conductor->ensureTaxiExists('available')->id;
                    }

                    $locked->update([
                        'conductor_id' => $conductor->id,
                        'taxi_id' => $taxiId,
                        'status' => 'accepted',
                    ]);

                    // Si el método es cartera virtual (app), el pago se realiza al aceptar.
                    if (($locked->pago_method ?? null) === 'app') {
                        // Evitar doble cobro si por cualquier razón ya existiera un pago.
                        $locked->loadMissing('pago');
                        if (!$locked->pago) {
                            $amount = (float) ($locked->price ?? 0);

                            // Bloquear y ajustar saldos de pasajero y conductor (usuario) de forma atómica.
                            $pasajero = User::query()->whereKey($locked->pasajero_id)->lockForUpdate()->first();
                            $conductorUser = User::query()->whereKey($conductor->user_id)->lockForUpdate()->first();

                            if (!$pasajero || !$conductorUser) {
                                throw new \RuntimeException('Usuarios no disponibles para procesar el pago');
                            }

                            $saldo = (float) ($pasajero->wallet_balance ?? 0);
                            if ($saldo < $amount) {
                                // Lanzar para forzar rollback (el viaje no se asigna si no se puede cobrar).
                                throw new \RuntimeException('Saldo insuficiente en la cartera virtual');
                            }

                            $pasajero->wallet_balance = $saldo - $amount;
                            $pasajero->save();

                            $conductorSaldo = (float) ($conductorUser->wallet_balance ?? 0);
                            $conductorUser->wallet_balance = $conductorSaldo + $amount;
                            $conductorUser->save();

                            $locked->pago()->create([
                                'amount' => $amount,
                                'method' => 'app',
                                'status' => 'paid',
                                'transaction_id' => 'wallet_' . $locked->id,
                            ]);
                        }
                    }

                    return $locked;
                }, 3);
            } catch (\RuntimeException $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            } catch (\Throwable $e) {
                report($e);
                return response()->json(['message' => 'Error al aceptar el viaje'], 500);
            }

            if ($viajeActualizado === null) {
                return response()->json(['message' => 'viaje not found'], 404);
            }

            if ($viajeActualizado === false) {
                return response()->json(['message' => 'viaje already accepted'], 409);
            }

            return response()->json($viajeActualizado->load(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
        }

        public function start(Request $solicitud, Viaje $viaje) {
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

        public function complete(Request $solicitud, Viaje $viaje) {
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

            // Si el pago es en efectivo o tarjeta, al completar lo marcamos como pagado.
            // En cartera virtual, el pago ya se creó al aceptar (pero dejamos un fallback por consistencia).
            $viaje->loadMissing('pago');
            if (!$viaje->pago) {
                $method = $viaje->pago_method ?? 'cash';
                if (in_array($method, ['cash', 'card', 'app'], true)) {
                    $viaje->pago()->create([
                        'amount' => (float) ($viaje->price ?? 0),
                        'method' => $method,
                        'status' => 'paid',
                        'transaction_id' => $method === 'app' ? ('wallet_' . $viaje->id) : null,
                    ]);
                }
            }

            return response()->json($viaje->fresh(['pasajero:id,name', 'conductor.user:id,name', 'taxi:id,plate,model', 'pago']));
        }

        public function reports() {
            $data = [
                'total' => Viaje::count(),
                'completed' => Viaje::where('status', 'completed')->count(),
                'cancelled' => Viaje::where('status', 'cancelled')->count(),
                'revenue' => Viaje::where('status', 'completed')->sum('price'),
            ];

            return response()->json($data);
        }

        public function rate(Request $solicitud, Viaje $viaje) {
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

        private function mapPaymentMethod($method) {
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