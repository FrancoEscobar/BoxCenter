<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tipo_entrenamiento_id')->constrained('tipos_entrenamiento')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('planes')->onDelete('cascade');
            $table->enum('estado', ['pago_pendiente', 'pago_rechazado', 'pago_cancelado', 'pago_fallido','activa', 'vencida', 'cancelada'])->default('pago_pendiente');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('descuento', 5,2)->default(0);
            $table->decimal('importe', 10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('membresias');
    }
};
