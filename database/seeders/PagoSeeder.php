<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pago;
use App\Models\Membresia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener membresías existentes
        $membresia1 = Membresia::where('usuario_id', 3)->first();
        $membresia2 = Membresia::where('usuario_id', 4)->first();
        
        // Obtener método de pago (Mercado Pago tiene ID 1)
        $metodoPagoId = DB::table('metodos_pago')->where('nombre', 'Mercado Pago')->first()->id ?? 1;

        // Pago aprobado para membresía activa
        if ($membresia1) {
            Pago::create([
                'membresia_id' => $membresia1->id,
                'payment_id' => 'MP-TEST-' . uniqid(),
                'fecha' => Carbon::now()->subDays(5),
                'detalle' => 'Pago de membresía mensual',
                'metodo_pago_id' => $metodoPagoId,
                'importe' => $membresia1->importe,
                'status' => 'approved',
                'payment_method_id' => 'credit_card',
                'payment_type_id' => 'credit_card',
                'authorization_code' => 'AUTH-' . rand(100000, 999999),
                'payer_email' => $membresia1->usuario->email ?? 'test@example.com',
                'installments' => 1,
                'date_approved' => Carbon::now()->subDays(5),
            ]);
        }

        // Pago aprobado para membresía vencida (pago antiguo)
        if ($membresia2) {
            Pago::create([
                'membresia_id' => $membresia2->id,
                'payment_id' => 'MP-TEST-' . uniqid(),
                'fecha' => Carbon::now()->subMonths(2),
                'detalle' => 'Pago de membresía mensual',
                'metodo_pago_id' => $metodoPagoId,
                'importe' => $membresia2->importe,
                'status' => 'approved',
                'payment_method_id' => 'debit_card',
                'payment_type_id' => 'debit_card',
                'authorization_code' => 'AUTH-' . rand(100000, 999999),
                'payer_email' => $membresia2->usuario->email ?? 'test2@example.com',
                'installments' => 1,
                'date_approved' => Carbon::now()->subMonths(2),
            ]);
        }

        // Pago pendiente (ejemplo)
        if ($membresia1) {
            Pago::create([
                'membresia_id' => $membresia1->id,
                'payment_id' => 'MP-TEST-' . uniqid(),
                'fecha' => Carbon::now()->subDays(1),
                'detalle' => 'Renovación de membresía pendiente',
                'metodo_pago_id' => $metodoPagoId,
                'importe' => $membresia1->importe,
                'status' => 'pending',
                'payment_method_id' => 'credit_card',
                'payment_type_id' => 'credit_card',
                'payer_email' => $membresia1->usuario->email ?? 'test@example.com',
                'installments' => 1,
            ]);
        }
    }
}
