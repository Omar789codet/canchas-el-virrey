<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CanchaController;
use App\Http\Controllers\Api\ReservaController;
use Illuminate\Support\Facades\Route;

// Consumidas por la PWA de clientes (public/pwa)

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/canchas', [CanchaController::class, 'index']);
Route::get('/canchas/{cancha}/disponibilidad', [CanchaController::class, 'disponibilidad']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas']);
    Route::post('/reservas', [ReservaController::class, 'store']);
});
