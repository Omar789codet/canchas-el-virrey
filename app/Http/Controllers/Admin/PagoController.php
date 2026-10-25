<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['reserva.cancha', 'reserva.cliente'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.pagos.index', compact('pagos'));
    }

    /**
     * Marcado manual: el administrador confirma que vio el comprobante
     * de pago por QR. No hay integración con token bancario (fuera de alcance).
     */
    public function marcarPagado(Pago $pago)
    {
        if ($pago->reserva->estado === 'cancelada') {
            return back()->with('error', 'No se puede marcar como pagada una reserva cancelada.');
        }

        $pago->update([
            'estado' => 'pagado',
            'verificado_por' => Auth::id(),
            'fecha_pago' => now(),
        ]);

        return back()->with('ok', 'Pago marcado como verificado.');
    }
}
