<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_cliente_nuevo_puede_registrarse_y_recibe_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Cliente de Prueba',
            'email' => 'cliente.prueba@example.com',
            'telefono' => '70000000',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', [
            'email' => 'cliente.prueba@example.com',
            'role' => 'cliente',
        ]);
    }

    public function test_no_se_puede_registrar_con_un_correo_ya_usado(): void
    {
        User::factory()->create(['email' => 'repetido@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Otro Usuario',
            'email' => 'repetido@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_con_credenciales_correctas_devuelve_token(): void
    {
        User::factory()->create([
            'email' => 'valido@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'valido@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['user', 'token']);
    }

    public function test_login_con_contrasena_incorrecta_devuelve_401(): void
    {
        User::factory()->create(['email' => 'valido2@example.com']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'valido2@example.com',
            'password' => 'contrasena-incorrecta',
        ]);

        $response->assertStatus(401);
    }
}
