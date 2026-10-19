@extends('layouts.admin')
@section('title', 'Editar cancha')
@section('content')
    <h3>Editar cancha</h3>
    <form action="{{ route('admin.canchas.update', $cancha) }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf @method('PUT')
        @include('admin.canchas._form')
        <button class="btn btn-primary">Actualizar</button>
    </form>
@endsection
