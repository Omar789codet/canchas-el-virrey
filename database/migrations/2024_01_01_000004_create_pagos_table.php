<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->decimal('monto', 8, 2);
            $table->enum('metodo', ['qr', 'efectivo'])->default('qr');
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete(); // admin que marcó el pago
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
