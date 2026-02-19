<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthSessionController extends Controller
{
    /**
     * Establecer sesiÃƒÂ³n de Laravel despuÃƒÂ©s del login API
     */
    public function establishSession(Request $solicitud)
    {
        // Obtener el token desde el query parameter
        $token = $solicitud->query('token');
        
        if (!$token) {
            return redirect('/')->with('error', 'Token requerido');
        }

        // Buscar el token en la tabla de Sanctum (plain token, no hasheado)
        // El token viene plain desde el cliente, necesito hashearlo para buscar en la BD
        $personalAccessToken = PersonalAccessToken::whereRaw('token = ?', [hash('sha256', $token)])->first();
        
        if (!$personalAccessToken) {
            return redirect('/')->with('error', 'Token invÃƒÂ¡lido');
        }

        $usuario = $personalAccessToken->tokenable;
        
        if (!$usuario) {
            return redirect('/')->with('error', 'Usuario no encontrado');
        }

        // Establecer sesiÃƒÂ³n de Laravel
        Auth::login($usuario);
        
        // Redirigir al dashboard correcto
        return redirect(match ($usuario->role) {
            'conductor' => '/conductor/dashboard',
            'admin' => '/admin/dashboard',
            default => '/dashboard/home',
        });
    }
}

