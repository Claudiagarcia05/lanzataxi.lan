<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function updateProfile(Request $solicitud)
    {
        $usuario = $solicitud->user();

        // Validar solo los campos que se envÃƒÂ­an
        $rules = [];
        if ($solicitud->has('name')) {
            $rules['name'] = 'string|max:255';
        }
        if ($solicitud->has('email')) {
            $rules['email'] = 'email|unique:users,email,' . $usuario->id;
        }
        if ($solicitud->has('phone')) {
            $rules['phone'] = 'nullable|string|max:20';
        }
        if ($solicitud->has('phone_alternative')) {
            $rules['phone_alternative'] = 'nullable|string|max:20';
        }
        if ($solicitud->hasFile('avatar')) {
            $rules['avatar'] = 'image|max:2048';
        }

        $validated = $solicitud->validate($rules);

        // Actualizar solo campos validados
        if (isset($validated['name'])) {
            $usuario->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $usuario->email = $validated['email'];
        }
        if (isset($validated['phone'])) {
            $usuario->phone = $validated['phone'];
        }

        if ($solicitud->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($usuario->avatar) {
                Storage::disk('public')->delete($usuario->avatar);
            }
            
            $path = $solicitud->file('avatar')->store('avatars', 'public');
            $usuario->avatar = $path;
        }

        $usuario->save();

        // Actualizar perfil de pasajero si existe
        if ($usuario->PerfilPasajero && isset($validated['phone_alternative'])) {
            $usuario->PerfilPasajero->update([
                'phone_alternative' => $validated['phone_alternative'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'user' => $usuario->fresh()
        ]);
    }

    public function uploadAvatar(Request $solicitud)
    {
        // Validar que el archivo existe
        if (!$solicitud->hasFile('avatar')) {
            return response()->json([
                'success' => false,
                'message' => 'No se recibiÃƒÂ³ ningÃƒÂºn archivo',
                'debug' => [
                    'has_file' => $solicitud->hasFile('avatar'),
                    'all_files' => $solicitud->allFiles(),
                    'content_type' => $solicitud->header('Content-Type')
                ]
            ], 422);
        }

        $file = $solicitud->file('avatar');

        // Validar el archivo manualmente con mejor mensajes de error
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo no es vÃƒÂ¡lido',
                'error' => $file->getErrorMessage()
            ], 422);
        }

        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/avif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, GIF, WEBP, AVIF',
                'mime_type' => $file->getMimeType()
            ], 422);
        }

        // Validar tamaÃƒÂ±o (2MB mÃƒÂ¡ximo)
        if ($file->getSize() > 2048 * 1024) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo es demasiado grande (mÃƒÂ¡ximo 2MB)'
            ], 422);
        }

        $usuario = $solicitud->user();

        // Eliminar avatar anterior si existe
        if ($usuario->avatar && Storage::disk('public')->exists($usuario->avatar)) {
            Storage::disk('public')->delete($usuario->avatar);
        }

        // Guardar nueva imagen
        $path = $file->store('avatars', 'public');
        $usuario->avatar = $path;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar actualizado correctamente',
            'avatar' => $path
        ]);
    }

    public function updatePassword(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $usuario = $solicitud->user();

        $usuario->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ContraseÃƒÂ±a actualizada correctamente'
        ]);
    }

    public function updatePreferences(Request $solicitud)
    {
        $validated = $solicitud->validate([
            'preferences' => 'required|array',
        ]);

        $usuario = $solicitud->user();

        // Si el usuario tiene perfil de pasajero, actualizar ahÃƒÂ­
        if ($usuario->PerfilPasajero) {
            $usuario->PerfilPasajero->update([
                'preferences' => json_encode($validated['preferences'])
            ]);
        } else {
            // Crear perfil de pasajero si no existe
            $usuario->PerfilPasajero()->create([
                'preferences' => json_encode($validated['preferences'])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Preferencias actualizadas correctamente'
        ]);
    }

    public function deleteAccount(Request $solicitud)
    {
        $usuario = $solicitud->user();
        
        // Eliminar avatar si existe
        if ($usuario->avatar) {
            Storage::disk('public')->delete($usuario->avatar);
        }
        
        // Soft delete del usuario
        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta eliminada correctamente'
        ]);
    }
}

