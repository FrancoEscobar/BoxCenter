<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('metodos_pago')->insert([
            [
                'nombre' => 'Mercado Pago',
                'descripcion' => 'Pagos realizados a través de la plataforma Mercado Pago',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago realizado en efectivo en el box',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Transferencia',
                'descripcion' => 'Pago realizado mediante transferencia bancaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}