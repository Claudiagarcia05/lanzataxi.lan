<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $solicitud)
    {
        // Por ahora devolvemos notificaciones simuladas
        // En producciÃƒÂ³n, usar Laravel Notifications + base de datos
        
        $notifications = [
            [
                'id' => 1,
                'type' => 'viaje_accepted',
                'title' => 'Viaje aceptado',
                'message' => 'Tu viaje ha sido aceptado por un conductor',
                'data' => ['viaje_id' => 1],
                'read_at' => null,
                'created_at' => now()->subMinutes(5)->toISOString(),
            ],
            [
                'id' => 2,
                'type' => 'viaje_completed',
                'title' => 'Viaje completado',
                'message' => 'Tu viaje ha sido completado. Ã‚Â¡No olvides valorar!',
                'data' => ['viaje_id' => 2],
                'read_at' => now()->subMinutes(2)->toISOString(),
                'created_at' => now()->subHours(1)->toISOString(),
            ],
        ];

        return response()->json($notifications);
    }

    public function markAsRead(Request $solicitud, $notificationId)
    {
        // En producciÃƒÂ³n: actualizar en base de datos
        
        return response()->json([
            'success' => true,
            'message' => 'NotificaciÃƒÂ³n marcada como leÃƒÂ­da'
        ]);
    }

    public function markAllAsRead(Request $solicitud)
    {
        // En producciÃƒÂ³n: actualizar todas las notificaciones del usuario
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leÃƒÂ­das'
        ]);
    }

    public function destroy(Request $solicitud, $notificationId)
    {
        // En producciÃƒÂ³n: eliminar notificaciÃƒÂ³n
        
        return response()->json([
            'success' => true,
            'message' => 'NotificaciÃƒÂ³n eliminada'
        ]);
    }
}

