<?php

namespace Tests\Feature\Admin;

use App\Models\Cancha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica la matriz RBAC (sección 4.5 del informe): el panel /admin solo
 * debe ser accesible para administrador y recepción, nunca para un cliente.
 */
class AccesoPanelAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_administrador_puede_entrar_al_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/canchas');

        $response->assertStatus(200);
    }

    public function test_recepcion_puede_entrar_al_panel(): void
    {
        $recepcion = User::factory()->create(['role' => 'recepcion']);

        $response = $this->actingAs($recepcion)->get('/admin/canchas');

        $response->assertStatus(200);
    }

    public function test_un_cliente_no_puede_entrar_al_panel(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $response = $this->actingAs($cliente)->get('/admin/canchas');

        $response->assertStatus(403);
    }

    public function test_un_visitante_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get('/admin/canchas');

        $response->assertRedirect('/login');
    }

    public function test_no_se_puede_eliminar_una_cancha_con_reservas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cliente = User::factory()->create(['role' => 'cliente']);
        $cancha = Cancha::create([
            'nombre' => 'Cancha con historial', 'tipo' => 'futbol', 'precio_hora' => 80, 'estado' => 'disponible',
        ]);
        $cancha->reservas()->create([
            'user_id' => $cliente->id, 'fecha' => '2026-12-01', 'hora_inicio' => '18:00', 'hora_fin' => '19:00',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/canchas/{$cancha->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('canchas', ['id' => $cancha->id]);
    }
}
