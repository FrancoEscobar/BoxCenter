<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clases', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->foreignId('tipo_entrenamiento_id')->constrained('tipos_entrenamiento')->onDelete('cascade');
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wod_id')->nullable()->constrained('wods')->onDelete('set null');
            $table->enum('estado', ['programada', 'realizada', 'cancelada'])->default('programada');
            $table->integer('cupo')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clases');
    }
};
