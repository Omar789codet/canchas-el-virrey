<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba no funcional de seguridad: confirma que un intento de inyección
 * SQL clásico, enviado en un campo de entrada, es rechazado/neutralizado.
 * Esto es posible porque Eloquent arma la consulta con sentencias
 * preparadas (bindings), nunca concatenando el texto del usuario en el SQL,
 * y porque la validación de Laravel filtra entradas mal formadas incluso
 * antes de llegar a la base de datos.
 */
class SeguridadInyeccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_payload_de_inyeccion_sql_en_el_login_no_otorga_acceso(): void
    {
        User::factory()->create(['email' => 'victima@example.com']);

        $response = $this->postJson('/api/auth/login', [
            'email' => "' OR '1'='1",
            'password' => "' OR '1'='1",
        ]);

        // El acceso nunca debe otorgarse (200) ni el servidor debe romperse
        // (500). Puede rechazarse en la validación del formato de correo
        // (422) o, con una entrada que sí pareciera un correo, al comparar
        // credenciales (401); ambos son un rechazo correcto.
        $response->assertStatus(422);
        $this->assertNotEquals(200, $response->status());
    }

    public function test_credenciales_invalidas_con_formato_valido_tampoco_otorgan_acceso(): void
    {
        User::factory()->create(['email' => 'victima2@example.com']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'victima2@example.com',
            'password' => "' OR '1'='1",
        ]);

        $response->assertStatus(401);
    }

    public function test_un_payload_de_inyeccion_en_la_busqueda_de_disponibilidad_no_rompe_la_consulta(): void
    {
        $cancha = \App\Models\Cancha::create([
            'nombre' => 'Cancha test', 'tipo' => 'futbol', 'precio_hora' => 80, 'estado' => 'disponible',
        ]);

        $response = $this->getJson(
            "/api/canchas/{$cancha->id}/disponibilidad?fecha=2026-01-01' OR '1'='1"
        );

        // La fecha mal formada debe ser rechazada por la validación (422),
        // no provocar un error de servidor ni devolver datos de otras fechas.
        $response->assertStatus(422);
    }
}
