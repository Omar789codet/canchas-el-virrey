<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cancha;
use App\Models\Reserva;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    public function index()
    {
        return Cancha::disponibles()->orderBy('nombre')->get();
    }

    /**
     * Horarios ya ocupados de una cancha en una fecha dada,
     * para que la PWA pinte el calendario de disponibilidad.
     */
    public function disponibilidad(Request $request, Cancha $cancha)
    {
        $request->validate(['fecha' => ['required', 'date']]);

        $ocupados = Reserva::where('cancha_id', $cancha->id)
            ->whereDate('fecha', $request->fecha)
            ->where('estado', '!=', 'cancelada')
            ->get(['hora_inicio', 'hora_fin']);

        return response()->json([
            'cancha' => $cancha,
            'fecha' => $request->fecha,
            'ocupados' => $ocupados,
        ]);
    }
}
