<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSessionLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_login_via_api_token(): void
    {
        // Crear usuario
        $usuario = User::factory()->create([
            'role' => 'pasajero',
            'email' => 'pasajero@test.com',
            'password' => bcrypt('password')
        ]);

        // Paso 1: POST /api/login
        echo "\n--- Paso 1: Login API ---\n";
        $respuestaLogin = $this->postJson('/api/login', [
            'email' => 'pasajero@test.com',
            'password' => 'password',
        ]);

        $respuestaLogin->assertStatus(200);
        $token = $respuestaLogin->json('token');
        echo "✓ Token recibido: " . substr($token, 0, 20) . "...\n";
        $this->assertNotEmpty($token);

        // Paso 2: GET /auth/session-login?token=...
        echo "\n--- Paso 2: Session Login ---\n";
        $respuestaSession = $this->get("/auth/session-login?token=" . urlencode($token));
        $respuestaSession->assertStatus(302); // Debe redirect
        echo "✓ Redirect recibido\n";

        // Paso 3: Me auth check después del redirect
        echo "\n--- Paso 3: Check Auth After Redirect ---\n";
        $this->assertAuthenticated();
        echo "✓ Usuario autenticado después del redirect\n";

        // Paso 4: Acceder a dashboard debería funcionar
        echo "\n--- Paso 4: Check Auth Persists ---\n";
        $this->assertAuthenticated();
        $this->assertEquals('pasajero@test.com', auth()->user()->email);
        echo "✓ Auth persists after all redirects\n";
    }
}
