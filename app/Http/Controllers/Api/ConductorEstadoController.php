<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Conductor;
use App\Http\Controllers\Controller;

class ConductorEstadoController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $conductor = Conductor::where('user_id', $user->id)->firstOrFail();
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);
        $conductor->is_active = $validated['is_active'];
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
        ]);
    }
}
