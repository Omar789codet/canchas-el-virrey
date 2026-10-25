<?php

namespace Tests\Feature\Api;

use App\Models\Cancha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservaApiTest extends TestCase
{
    use RefreshDatabase;

    protected Cancha $cancha;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cancha = Cancha::create([
            'nombre' => 'Cancha 1 - Fútbol',
            'tipo' => 'futbol',
            'capacidad' => 10,
            'precio_hora' => 80,
            'estado' => 'disponible',
        ]);
    }

    public function test_un_cliente_autenticado_puede_reservar(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_inicio' => '18:00',
            'hora_fin' => '19:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('reserva.user_id', $cliente->id)
            ->assertJsonPath('pago.estado', 'pendiente')
            ->assertJsonPath('pago.monto', 80);
    }

    public function test_no_se_puede_reservar_un_horario_ya_ocupado(): void
    {
        $clienteA = User::factory()->create(['role' => 'cliente']);
        $clienteB = User::factory()->create(['role' => 'cliente']);
        $fecha = now()->addDay()->toDateString();

        Sanctum::actingAs($clienteA);
        $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => $fecha,
            'hora_inicio' => '18:00',
            'hora_fin' => '19:00',
        ])->assertStatus(201);

        Sanctum::actingAs($clienteB);
        $response = $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => $fecha,
            'hora_inicio' => '18:30',
            'hora_fin' => '19:30',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('hora_inicio');
    }

    public function test_un_visitante_sin_token_no_puede_reservar(): void
    {
        $response = $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_inicio' => '18:00',
            'hora_fin' => '19:00',
        ]);

        $response->assertStatus(401);
    }

    public function test_no_se_puede_reservar_una_cancha_en_mantenimiento(): void
    {
        $this->cancha->update(['estado' => 'mantenimiento']);
        $cliente = User::factory()->create(['role' => 'cliente']);
        Sanctum::actingAs($cliente);

        $response = $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_inicio' => '18:00',
            'hora_fin' => '19:00',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('cancha_id');
    }

    public function test_un_cliente_ve_solo_sus_propias_reservas(): void
    {
        $clienteA = User::factory()->create(['role' => 'cliente']);
        $clienteB = User::factory()->create(['role' => 'cliente']);

        Sanctum::actingAs($clienteA);
        $this->postJson('/api/reservas', [
            'cancha_id' => $this->cancha->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
        ]);

        Sanctum::actingAs($clienteB);
        $response = $this->getJson('/api/mis-reservas');

        $response->assertStatus(200)->assertJsonCount(0);
    }
}
