<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cancha->nombre ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Tipo</label>
    <select name="tipo" class="form-select">
        @foreach (['futbol' => 'Fútbol', 'basquet' => 'Básquet', 'voley' => 'Vóley', 'tenis' => 'Tenis', 'otro' => 'Otro'] as $value => $label)
            <option value="{{ $value }}" @selected(old('tipo', $cancha->tipo ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Capacidad</label>
    <input type="number" name="capacidad" class="form-control" value="{{ old('capacidad', $cancha->capacidad ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Precio por hora (Bs.)</label>
    <input type="number" step="0.01" name="precio_hora" class="form-control" value="{{ old('precio_hora', $cancha->precio_hora ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="estado" class="form-select">
        @foreach (['disponible' => 'Disponible', 'mantenimiento' => 'Mantenimiento', 'inactiva' => 'Inactiva'] as $value => $label)
            <option value="{{ $value }}" @selected(old('estado', $cancha->estado ?? 'disponible') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
