<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cancha_id', 'user_id', 'creado_por', 'fecha', 'hora_inicio', 'hora_fin', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    /**
     * Regla de negocio central: ¿esta cancha ya tiene una reserva
     * (no cancelada) que se cruza con el horario solicitado?
     *
     * Usamos whereDate() en vez de where('fecha', ...) porque comparar
     * la fecha como texto exacto es frágil: el formato de almacenamiento
     * puede variar ligeramente según el motor de base de datos (esto se
     * detectó con las pruebas automatizadas, que corren sobre SQLite).
     * whereDate() normaliza la comparación a nivel de fecha sin importar
     * el motor usado.
     */
    public static function existeCruce(int $canchaId, string $fecha, string $horaInicio, string $horaFin, ?int $ignorarReservaId = null): bool
    {
        return static::where('cancha_id', $canchaId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->when($ignorarReservaId, fn ($q) => $q->where('id', '!=', $ignorarReservaId))
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }
}
