<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membresia_id')->constrained('membresias')->onDelete('cascade');
            $table->string('payment_id')->unique(); // ID de Mercado Pago
            $table->date('fecha');
            $table->text('detalle')->nullable();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->onDelete('cascade');
            $table->decimal('importe', 10, 2);
            $table->string('comprobante', 255)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('payment_method_id', 50)->nullable();
            $table->string('payment_type_id', 50)->nullable();
            $table->string('authorization_code', 100)->nullable();
            $table->string('payer_email', 100)->nullable();
            $table->integer('installments')->nullable();
            $table->timestamp('date_approved')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};