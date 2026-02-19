<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Obtener balance de la cartera
     */
    public function getBalance()
    {
        $usuario = Auth::user();
        
        // Si no existe el campo wallet_balance en la tabla users, devolver 0
        $balance = $usuario->wallet_balance ?? 0;
        
        return response()->json([
            'balance' => floatval($balance),
            'currency' => 'EUR'
        ]);
    }

    /**
     * Obtener historial de transacciones
     */
    public function getTransactions()
    {
        $usuario = Auth::user();
        
        // En producciÃƒÂ³n, esto vendrÃƒÂ­a de una tabla wallet_transactions
        // Por ahora devolvemos un array vacÃƒÂ­o para usuarios nuevos
        return response()->json([]);
    }

    /**
     * AÃƒÂ±adir fondos a la cartera
     */
    public function addFunds(Request $solicitud)
    {
        $solicitud->validate([
            'amount' => 'required|numeric|min:5|max:1000'
        ]);

        $usuario = Auth::user();
        $amount = floatval($solicitud->amount);
        
        // Actualizar balance
        $currentBalance = $usuario->wallet_balance ?? 0;
        $newBalance = $currentBalance + $amount;
        
        $usuario->wallet_balance = $newBalance;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Saldo aÃƒÂ±adido correctamente',
            'new_balance' => $newBalance,
            'transaction' => [
                'id' => rand(1000, 9999),
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Recarga de saldo',
                'created_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Usar fondos de la cartera para un viaje
     */
    public function useFunds(Request $solicitud)
    {
        $solicitud->validate([
            'amount' => 'required|numeric|min:0.01',
            'viaje_id' => 'required|integer|exists:viajes,id'
        ]);

        $usuario = Auth::user();
        $amount = floatval($solicitud->amount);
        $currentBalance = $usuario->wallet_balance ?? 0;

        // Verificar que hay saldo suficiente
        if ($currentBalance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente',
                'current_balance' => $currentBalance,
                'required_amount' => $amount
            ], 400);
        }

        // Descontar del balance
        $newBalance = $currentBalance - $amount;
        $usuario->wallet_balance = $newBalance;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Pago procesado correctamente',
            'new_balance' => $newBalance,
            'transaction' => [
                'id' => rand(1000, 9999),
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Pago de viaje #' . $solicitud->viaje_id,
                'created_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Retirar fondos de la cartera
     */
    public function withdrawFunds(Request $solicitud)
    {
        $solicitud->validate([
            'amount' => 'required|numeric|min:5'
        ]);

        $usuario = Auth::user();
        $amount = floatval($solicitud->amount);
        $currentBalance = $usuario->wallet_balance ?? 0;

        // Verificar que hay saldo suficiente
        if ($currentBalance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente para retirar',
                'current_balance' => $currentBalance,
                'requested_amount' => $amount
            ], 400);
        }

        // Descontar del balance
        $newBalance = $currentBalance - $amount;
        $usuario->wallet_balance = $newBalance;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de retirada procesada',
            'new_balance' => $newBalance,
            'transaction' => [
                'id' => rand(1000, 9999),
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Retiro de fondos',
                'created_at' => now()->toISOString()
            ]
        ]);
    }
}

