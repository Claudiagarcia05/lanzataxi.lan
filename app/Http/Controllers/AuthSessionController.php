<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthSessionController extends Controller
{
    /**
     * Establecer sesión de Laravel después del login API
     * 
     * Flujo:
     * 1. Modal SPA POST /api/login → obtiene token Sanctum
     * 2. Modal redirige a /auth/session-login?token=...
     * 3. Este controlador valida el token y establece sesión web
     * 4. Redirige al dashboard según el rol del usuario
     */
    public function establishSession(Request $solicitud)
    {
        // Obtener el token desde el query parameter
        $token = $solicitud->query('token');
        
        if (!$token) {
            return redirect('/')->with('error', 'Token requerido');
        }

        // Buscar el token usando el helper oficial de Sanctum
        $personalAccessToken = PersonalAccessToken::findToken($token);
        
        if (!$personalAccessToken) {
            return redirect('/')->with('error', 'Token inválido');
        }

        $usuario = $personalAccessToken->tokenable;
        
        if (!$usuario) {
            return redirect('/')->with('error', 'Usuario no encontrado');
        }

        // Loguear al usuario en el guard web
        // Laravel's StartSession middleware se encargará de la persistencia
        Auth::guard('web')->login($usuario, remember: true);
        
        // Log para debugging
        \Log::info('User authenticated via API token', [
            'user_id' => $usuario->id,
            'email' => $usuario->email,
        ]);
        
        // Redirigir al dashboard según el rol
        return redirect(match ($usuario->role) {
            'conductor' => '/conductor/dashboard',
            'admin' => '/admin/dashboard',
            default => '/pasajero/home',
        });
    }
}

