<?php

namespace App\Http\Requests;

use App\Rules\HorarioDisponible;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Si no viene user_id en el body (caso de la PWA), se usa el cliente autenticado.
        if (! $this->filled('user_id') && $this->user()) {
            $this->merge(['user_id' => $this->user()->id]);
        }
    }

    public function rules(): array
    {
        $reservaId = $this->route('reserva')?->id;

        return [
            'cancha_id' => [
                'required',
                Rule::exists('canchas', 'id')->where('estado', 'disponible'),
            ],
            'user_id' => ['required', 'exists:users,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'hora_inicio' => [
                'required',
                'date_format:H:i',
                new HorarioDisponible(
                    canchaId: (int) $this->input('cancha_id'),
                    fecha: (string) $this->input('fecha'),
                    horaFin: (string) $this->input('hora_fin'),
                    ignorarReservaId: $reservaId,
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'fecha.after_or_equal' => 'No se pueden crear reservas en fechas pasadas.',
            'cancha_id.exists' => 'Esta cancha no está disponible para reservar (en mantenimiento o inactiva).',
        ];
    }
}
