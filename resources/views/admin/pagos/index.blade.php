@extends('layouts.admin')
@section('title', 'Pagos')
@section('content')
    <h3>Pagos</h3>
    <p class="text-muted">La verificación del pago por QR es manual: el administrador confirma aquí que vio el comprobante, sin integración con el banco.</p>
    <table class="table bg-white">
        <thead>
            <tr><th>Reserva</th><th>Cliente</th><th>Monto</th><th>Método</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($pagos as $pago)
            <tr>
                <td>{{ $pago->reserva->cancha->nombre }} — {{ $pago->reserva->fecha->format('d/m/Y') }}</td>
                <td>{{ $pago->reserva->cliente->name }}</td>
                <td>Bs. {{ number_format($pago->monto, 2) }}</td>
                <td>{{ strtoupper($pago->metodo) }}</td>
                <td><span class="badge bg-{{ $pago->estado === 'pagado' ? 'success' : 'warning' }}">{{ $pago->estado }}</span></td>
                <td class="text-end">
                    @if ($pago->estado === 'pendiente')
                        <form action="{{ route('admin.pagos.marcarPagado', $pago) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Marcar como pagado</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $pagos->links() }}
@endsection
