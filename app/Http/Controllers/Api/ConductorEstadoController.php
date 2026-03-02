<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Conductor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ConductorEstadoController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $conductor = Conductor::where('user_id', $user->id)->firstOrFail();
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $now = Carbon::now();
        $wasActive = (bool) $conductor->is_active;
        $willBeActive = (bool) $validated['is_active'];

        $monthKey = $now->format('Y-m');
        if (($conductor->online_month ?? null) !== $monthKey) {
            $conductor->online_month = $monthKey;
            $conductor->online_seconds_month = 0;
            $conductor->online_since = $willBeActive ? $now : null;
        }

        if ($wasActive && !$willBeActive) {
            if ($conductor->online_since) {
                $elapsed = $conductor->online_since->diffInSeconds($now);
                $conductor->online_seconds = (int) ($conductor->online_seconds ?? 0) + (int) $elapsed;
                $conductor->online_seconds_month = (int) ($conductor->online_seconds_month ?? 0) + (int) $elapsed;
                $conductor->online_since = null;
            }
        } elseif (!$wasActive && $willBeActive) {
            if (!$conductor->online_since) {
                $conductor->online_since = $now;
            }
        } elseif ($willBeActive && !$conductor->online_since) {
            $conductor->online_since = $now;
        }

        $conductor->is_active = $willBeActive;
        $conductor->save();

        if ($conductor->is_active) {
            $taxi = $conductor->ensureTaxiExists('available');
            if ($taxi->status === 'offline') {
                $taxi->update(['status' => 'available']);
            }
        } else {
            $taxi = $conductor->taxi;
            if ($taxi && $taxi->status !== 'offline') {
                $taxi->update(['status' => 'offline']);
            }
        }

        return response()->json([
            'success' => true,
            'is_active' => $conductor->is_active,
            'taxi' => $conductor->taxi,
            'connected_seconds' => (int) ($conductor->online_seconds ?? 0) + ($conductor->is_active && $conductor->online_since ? $conductor->online_since->diffInSeconds($now) : 0),
            'connected_hours' => round((((int) ($conductor->online_seconds ?? 0) + ($conductor->is_active && $conductor->online_since ? $conductor->online_since->diffInSeconds($now) : 0)) / 3600), 2),
            'connected_seconds_month' => (int) ($conductor->online_seconds_month ?? 0) + ($conductor->is_active && $conductor->online_since ? $conductor->online_since->diffInSeconds($now) : 0),
            'connected_hours_month' => round((((int) ($conductor->online_seconds_month ?? 0) + ($conductor->is_active && $conductor->online_since ? $conductor->online_since->diffInSeconds($now) : 0)) / 3600), 2),
            'online_month' => $conductor->online_month,
            'online_since' => $conductor->online_since?->toIso8601String(),
        ]);
    }
}
