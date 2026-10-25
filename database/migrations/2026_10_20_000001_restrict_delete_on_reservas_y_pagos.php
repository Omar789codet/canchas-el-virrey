<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Antes: eliminar una cancha o un usuario borraba en cascada todas sus
 * reservas y pagos, perdiendo el historial que el sistema está pensado
 * para preservar. Ahora: no se puede eliminar una cancha/usuario/reserva
 * que tenga registros asociados (se debe desactivar en vez de borrar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['cancha_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('cancha_id')->references('id')->on('canchas')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['reserva_id']);
            $table->foreign('reserva_id')->references('id')->on('reservas')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['cancha_id']);
            $table->dropForeign(['user_id']);
            $table->foreign('cancha_id')->references('id')->on('canchas')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['reserva_id']);
            $table->foreign('reserva_id')->references('id')->on('reservas')->cascadeOnDelete();
        });
    }
};
