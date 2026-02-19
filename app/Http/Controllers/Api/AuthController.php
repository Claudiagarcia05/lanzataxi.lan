<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:pasajero,conductor,admin',
            'phone' => 'nullable|string|max:50',
        ]);

        $usuario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'pasajero',
            'phone' => $validated['phone'] ?? null,
            'wallet_balance' => 0,
        ]);

        // Crear token para autenticar automÃƒÂ¡ticamente
        $token = $usuario->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
            'user' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'phone' => $usuario->phone,
                'avatar' => $usuario->avatar,
                'wallet_balance' => $usuario->wallet_balance ?? 0,
            ],
        ], 201);
    }

    public function login(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = User::where('email', $validated['email'])->first();

        if (!$usuario || !Hash::check($validated['password'], $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales invÃƒÂ¡lidas. Por favor verifica tu email y contraseÃƒÂ±a.',
                'errors' => [
                    'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.']
                ]
            ], 401);
        }

        // Eliminar tokens anteriores del usuario
        $usuario->tokens()->delete();

        // Crear nuevo token
        $token = $usuario->createToken('api')->plainTextToken;

        // Ã¢Â­Â ESTABLECER SESIÃƒâ€œN DE LARAVEL AQUÃƒÂ
        // Esto hace que Laravel reconozca al usuario en peticiones posteriores
        auth()->login($usuario);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'phone' => $usuario->phone,
                'avatar' => $usuario->avatar,
                'wallet_balance' => $usuario->wallet_balance ?? 0,
            ],
            'message' => 'Inicio de sesiÃƒÂ³n exitoso'
        ]);
    }

    public function me(Request $solicitud)
    {
        return response()->json($solicitud->user());
    }

    public function logout(Request $solicitud)
    {
        $solicitud->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'SesiÃƒÂ³n cerrada']);
    }
}

