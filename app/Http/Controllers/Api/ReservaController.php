<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function misReservas(Request $request)
    {
        return $request->user()
            ->reservas()
            ->with(['cancha', 'pago'])
            ->orderByDesc('fecha')
            ->get();
    }

    public function store(StoreReservaRequest $request)
    {
        // El cliente solo puede reservar para sí mismo desde la PWA
        $reserva = Reserva::create([
            'cancha_id' => $request->validated('cancha_id'),
            'user_id' => $request->user()->id,
            'fecha' => $request->validated('fecha'),
            'hora_inicio' => $request->validated('hora_inicio'),
            'hora_fin' => $request->validated('hora_fin'),
            'estado' => 'pendiente',
        ]);

        $cancha = $reserva->cancha;
        $horas = (strtotime($reserva->hora_fin) - strtotime($reserva->hora_inicio)) / 3600;

        $pago = Pago::create([
            'reserva_id' => $reserva->id,
            'monto' => round($cancha->precio_hora * $horas, 2),
            'metodo' => 'qr',
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'reserva' => $reserva->load('cancha'),
            'pago' => $pago,
            'mensaje' => 'Reserva registrada. Paga con QR en recepción; el administrador confirmará el pago.',
        ], 201);
    }
}
