<?php

namespace Tests\Unit;

use App\Models\Cancha;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El corazón del sistema: Reserva::existeCruce es la función que impide
 * la doble reserva. Si esta prueba falla, el requisito más importante
 * del proyecto (evitar que dos clientes reserven la misma cancha en el
 * mismo horario) no se está cumpliendo.
 */
class HorarioDisponibleTest extends TestCase
{
    use RefreshDatabase;

    protected Cancha $cancha;
    protected User $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cancha = Cancha::create([
            'nombre' => 'Cancha de prueba',
            'tipo' => 'futbol',
            'capacidad' => 10,
            'precio_hora' => 80,
            'estado' => 'disponible',
        ]);

        $this->cliente = User::factory()->create(['role' => 'cliente']);
    }

    protected function crearReserva(string $horaInicio, string $horaFin, string $estado = 'confirmada'): Reserva
    {
        return Reserva::create([
            'cancha_id' => $this->cancha->id,
            'user_id' => $this->cliente->id,
            'fecha' => '2026-12-01',
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'estado' => $estado,
        ]);
    }

    public function test_detecta_cruce_cuando_el_horario_se_superpone(): void
    {
        $this->crearReserva('18:00', '19:00');

        $hayCruce = Reserva::existeCruce($this->cancha->id, '2026-12-01', '18:30', '19:30');

        $this->assertTrue($hayCruce, 'Debería detectar el cruce entre 18:00-19:00 y 18:30-19:30.');
    }

    public function test_no_detecta_cruce_cuando_el_horario_es_consecutivo(): void
    {
        $this->crearReserva('18:00', '19:00');

        $hayCruce = Reserva::existeCruce($this->cancha->id, '2026-12-01', '19:00', '20:00');

        $this->assertFalse($hayCruce, 'Un horario que empieza justo cuando el otro termina NO debería cruzar.');
    }

    public function test_no_detecta_cruce_en_otra_cancha(): void
    {
        $this->crearReserva('18:00', '19:00');
        $otraCancha = Cancha::create([
            'nombre' => 'Otra cancha', 'tipo' => 'basquet', 'precio_hora' => 60, 'estado' => 'disponible',
        ]);

        $hayCruce = Reserva::existeCruce($otraCancha->id, '2026-12-01', '18:00', '19:00');

        $this->assertFalse($hayCruce, 'El mismo horario en una cancha distinta no debe considerarse un cruce.');
    }

    public function test_ignora_reservas_canceladas(): void
    {
        $this->crearReserva('18:00', '19:00', estado: 'cancelada');

        $hayCruce = Reserva::existeCruce($this->cancha->id, '2026-12-01', '18:00', '19:00');

        $this->assertFalse($hayCruce, 'Una reserva cancelada no debería bloquear el horario.');
    }

    public function test_ignora_la_propia_reserva_al_editar(): void
    {
        $reserva = $this->crearReserva('18:00', '19:00');

        $hayCruce = Reserva::existeCruce(
            $this->cancha->id, '2026-12-01', '18:00', '19:00', ignorarReservaId: $reserva->id
        );

        $this->assertFalse($hayCruce, 'Al editar una reserva, no debe chocar consigo misma.');
    }
}
