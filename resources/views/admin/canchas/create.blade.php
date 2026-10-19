@extends('layouts.admin')
@section('title', 'Nueva cancha')
@section('content')
    <h3>Nueva cancha</h3>
    <form action="{{ route('admin.canchas.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        @include('admin.canchas._form')
        <button class="btn btn-primary">Guardar</button>
    </form>
@endsection
