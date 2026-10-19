@extends('layouts.admin')
@section('title', 'Nueva reserva')
@section('content')
    @php
        $opcionesHora = '';
        for ($h = 0; $h < 24; $h++) {
            foreach ([0, 30] as $m) {
                $valor = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                $hora12 = $h % 12 === 0 ? 12 : $h % 12;
                $ampm = $h < 12 ? 'AM' : 'PM';
                $etiqueta = $hora12 . ':' . str_pad($m, 2, '0', STR_PAD_LEFT) . ' ' . $ampm;
                $opcionesHora .= "<option value=\"{$valor}\">{$etiqueta}</option>";
            }
        }
    @endphp

    <h3>Nueva reserva (presencial)</h3>
    <form action="{{ route('admin.reservas.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Cancha</label>
            <select name="cancha_id" class="form-select" required>
                @foreach ($canchas as $cancha)
                    <option value="{{ $cancha->id }}">{{ $cancha->nombre }} (Bs. {{ $cancha->precio_hora }}/h)</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="user_id" class="form-select" required>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                @endforeach
            </select>
            <div class="form-text">Si el cliente no existe aún, regístralo primero desde la gestión de usuarios.</div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Hora inicio</label>
                <select name="hora_inicio" class="form-select" required>{!! $opcionesHora !!}</select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Hora fin</label>
                <select name="hora_fin" class="form-select" required>{!! $opcionesHora !!}</select>
            </div>
        </div>
        <button class="btn btn-primary">Registrar reserva</button>
    </form>
@endsection
