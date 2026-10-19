@extends('layouts.admin')
@section('title', 'Canchas')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Canchas</h3>
        <a href="{{ route('admin.canchas.create') }}" class="btn btn-primary">+ Nueva cancha</a>
    </div>
    <table class="table bg-white">
        <thead>
            <tr><th>Nombre</th><th>Tipo</th><th>Capacidad</th><th>Precio/hora</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($canchas as $cancha)
            <tr>
                <td>{{ $cancha->nombre }}</td>
                <td>{{ ucfirst($cancha->tipo) }}</td>
                <td>{{ $cancha->capacidad ?? '-' }}</td>
                <td>Bs. {{ number_format($cancha->precio_hora, 2) }}</td>
                <td><span class="badge bg-{{ $cancha->estado === 'disponible' ? 'success' : 'secondary' }}">{{ $cancha->estado }}</span></td>
                <td class="text-end">
                    <a href="{{ route('admin.canchas.edit', $cancha) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <form action="{{ route('admin.canchas.destroy', $cancha) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta cancha?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $canchas->links() }}
@endsection
