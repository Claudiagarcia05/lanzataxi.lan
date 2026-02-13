<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Trip;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Trip $trip)
    {
        if ($trip->payment) {
            return response()->json(['message' => 'Payment already exists'], 409);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,paypal',
            'status' => 'nullable|in:pending,paid,refunded',
        ]);

        $payment = Payment::create([
            'trip_id' => $trip->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'status' => $validated['status'] ?? 'paid',
        ]);

        return response()->json($payment, 201);
    }
}
