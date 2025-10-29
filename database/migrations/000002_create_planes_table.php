<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('duracion'); // días
            $table->decimal('precio', 10,2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('planes');
    }
};
