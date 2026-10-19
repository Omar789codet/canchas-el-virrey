<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancha extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'tipo', 'capacidad', 'precio_hora', 'estado',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }
}
