<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\pago;
use App\Models\viaje;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function store(Request $solicitud, viaje $viaje)
    {
        if ($viaje->pasajero_id !== $solicitud->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($viaje->status !== 'completed') {
            return response()->json(['message' => 'Solo se puede pagar viajes completados'], 400);
        }

        if ($viaje->pago) {
            return response()->json(['message' => 'Este viaje ya ha sido pagado'], 400);
        }

        $validated = $solicitud->validate([
            'method' => 'required|string|in:cash,card,app,stripe,paypal',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $pago = pago::create([
            'viaje_id' => $viaje->id,
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'status' => 'paid',
            'transaction_id' => $validated['transaction_id'] ?? null,
        ]);

        return response()->json($pago->load('viaje'), 201);
    }

    public function show(viaje $viaje)
    {
        if ($viaje->pasajero_id !== auth()->id() && $viaje->conductor?->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pago = $viaje->pago;

        if (!$pago) {
            return response()->json(['message' => 'No se encontrÃƒÂ³ pago para este viaje'], 404);
        }

        return response()->json($pago);
    }

    public function processstripe(Request $solicitud, viaje $viaje)
    {
        $validated = $solicitud->validate([
            'pago_method_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // AquÃƒÂ­ irÃƒÂ­a la integraciÃƒÂ³n real con stripe
        // Por ahora simulamos el proceso
        
        try {
            // Simular procesamiento de pago
            // En producciÃƒÂ³n: \stripe\pagoIntent::create([...])
            
            $pago = pago::create([
                'viaje_id' => $viaje->id,
                'method' => 'stripe',
                'amount' => $validated['amount'],
                'status' => 'paid',
                'transaction_id' => 'sim_' . uniqid(),
            ]);

            return response()->json([
                'success' => true,
                'pago' => $pago,
                'message' => 'Pago procesado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPayPal(Request $solicitud, viaje $viaje)
    {
        $validated = $solicitud->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // AquÃƒÂ­ irÃƒÂ­a la integraciÃƒÂ³n real con PayPal
        // Por ahora simulamos el proceso
        
        try {
            $pago = pago::create([
                'viaje_id' => $viaje->id,
                'method' => 'paypal',
                'amount' => $validated['amount'],
                'status' => 'paid',
                'transaction_id' => $validated['order_id'],
            ]);

            return response()->json([
                'success' => true,
                'pago' => $pago,
                'message' => 'Pago procesado correctamente con PayPal'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}

