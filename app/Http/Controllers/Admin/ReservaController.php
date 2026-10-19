<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Models\Cancha;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['cancha', 'cliente', 'pago'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio')
            ->paginate(15);

        return view('admin.reservas.index', compact('reservas'));
    }

    public function create()
    {
        $canchas = Cancha::disponibles()->orderBy('nombre')->get();
        $clientes = User::where('role', 'cliente')->orderBy('name')->get();

        return view('admin.reservas.create', compact('canchas', 'clientes'));
    }

    public function store(StoreReservaRequest $request)
    {
        $reserva = Reserva::create([
            ...$request->validated(),
            'creado_por' => Auth::id(),
            'estado' => 'confirmada',
        ]);

        // Se genera automáticamente el registro de pago pendiente asociado
        $cancha = $reserva->cancha;
        $horas = (strtotime($reserva->hora_fin) - strtotime($reserva->hora_inicio)) / 3600;

        Pago::create([
            'reserva_id' => $reserva->id,
            'monto' => round($cancha->precio_hora * $horas, 2),
            'metodo' => 'qr',
            'estado' => 'pendiente',
        ]);

        return redirect()->route('admin.reservas.index')->with('ok', 'Reserva registrada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->update(['estado' => 'cancelada']);
        return back()->with('ok', 'Reserva cancelada.');
    }
}
