@extends('layouts.admin')
@section('title', 'Reservas')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Reservas</h3>
        <a href="{{ route('admin.reservas.create') }}" class="btn btn-primary">+ Nueva reserva</a>
    </div>
    <table class="table bg-white">
        <thead>
            <tr><th>Cancha</th><th>Cliente</th><th>Fecha</th><th>Horario</th><th>Estado</th><th>Pago</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($reservas as $reserva)
            <tr>
                <td>{{ $reserva->cancha->nombre }}</td>
                <td>{{ $reserva->cliente->name }}</td>
                <td>{{ $reserva->fecha->format('d/m/Y') }}</td>
                <td>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</td>
                <td><span class="badge bg-{{ $reserva->estado === 'confirmada' ? 'success' : ($reserva->estado === 'cancelada' ? 'secondary' : 'warning') }}">{{ $reserva->estado }}</span></td>
                <td>
                    @if ($reserva->pago)
                        <span class="badge bg-{{ $reserva->pago->estado === 'pagado' ? 'success' : 'warning' }}">{{ $reserva->pago->estado }}</span>
                    @else
                        -
                    @endif
                </td>
                <td class="text-end">
                    @if ($reserva->estado !== 'cancelada')
                        <form action="{{ route('admin.reservas.destroy', $reserva) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Cancelar esta reserva?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $reservas->links() }}
@endsection
