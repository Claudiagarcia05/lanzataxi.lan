<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Debt;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class WalletController extends Controller {
        public function getBalance() {
            $usuario = Auth::user();
            
            $balance = $usuario->wallet_balance ?? 0;
            
            return response()->json([
                'balance' => floatval($balance),
                'currency' => 'EUR'
            ]);
        }

        public function getTransactions() {
            $usuario = Auth::user();
            
            return response()->json([]);
        }

        public function getDebtSummary() {
            $usuario = Auth::user();
            $pendingDebt = Debt::where('user_id', $usuario->id)
                ->where('status', 'pending')
                ->sum('amount');

            return response()->json([
                'pending_debt' => floatval($pendingDebt),
                'currency' => 'EUR'
            ]);
        }

        public function addFunds(Request $solicitud) {
            $solicitud->validate([
                'amount' => 'required|numeric|min:5|max:1000'
            ]);

            $usuario = Auth::user();
            $amount = floatval($solicitud->amount);
            
            $currentBalance = $usuario->wallet_balance ?? 0;
            $newBalance = $currentBalance + $amount;
            
            $usuario->wallet_balance = $newBalance;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Saldo añadido correctamente',
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

        public function useFunds(Request $solicitud) {
            $solicitud->validate([
                'amount' => 'required|numeric|min:0.01',
                'viaje_id' => 'required|integer|exists:viajes,id'
            ]);

            $usuario = Auth::user();
            $amount = floatval($solicitud->amount);
            $currentBalance = $usuario->wallet_balance ?? 0;

            if ($currentBalance < $amount) {

                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente',
                    'current_balance' => $currentBalance,
                    'required_amount' => $amount
                ], 400);
            }

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

        public function withdrawFunds(Request $solicitud) {
            $solicitud->validate([
                'amount' => 'required|numeric|min:5'
            ]);

            $usuario = Auth::user();
            $amount = floatval($solicitud->amount);
            $currentBalance = $usuario->wallet_balance ?? 0;

            if ($currentBalance < $amount) {

                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente para retirar',
                    'current_balance' => $currentBalance,
                    'requested_amount' => $amount
                ], 400);
            }

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