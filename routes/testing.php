<?php

use App\Http\Controllers\AuthSessionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Ruta para testing del flujo completo
Route::get('/test-login-flow', function () {
    // Crear usuario de test
    User::where('email', 'test@test.com')->delete();
    
    $usuario = User::create([
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => bcrypt('password'),
        'role' => 'pasajero'
    ]);
    
    // Generar token
    $token = $usuario->createToken('api')->plainTextToken;
    
    return response()->json([
        'token' => $token,
        'session_login_url' => "/auth/session-login?token=" . urlencode($token),
        'user_id' => $usuario->id,
        'instructions' => [
            '1. Navigate to session_login_url in browser',
            '2. Check if you land on /dashboard/home or /login',
            '3. Check browser console for logs'
        ]
    ]);
});

// Ruta para verificar que la sesión está establecida
Route::get('/test-check-session', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role
        ] : null,
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
});
