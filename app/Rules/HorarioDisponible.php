<?php

namespace App\Rules;

use App\Models\Reserva;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HorarioDisponible implements ValidationRule
{
    public function __construct(
        protected int $canchaId,
        protected string $fecha,
        protected string $horaFin,
        protected ?int $ignorarReservaId = null,
    ) {}

    /**
     * $value aquí es la hora_inicio que se está validando.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Reserva::existeCruce($this->canchaId, $this->fecha, $value, $this->horaFin, $this->ignorarReservaId)) {
            $fail('La cancha ya tiene una reserva registrada que se cruza con ese horario.');
        }
    }
}
