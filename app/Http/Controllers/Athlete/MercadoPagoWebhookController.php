<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MembershipService;
use App\Services\PaymentGateway\MercadoPagoService;
use Illuminate\Support\Facades\Log;
use App\Models\Pago;
use App\Models\Membresia;

class MercadoPagoWebhookController extends Controller
{
    protected $membershipService;
    protected $paymentService;

    public function __construct(MembershipService $membershipService, MercadoPagoService $paymentService)
    {
        $this->membershipService = $membershipService;
        $this->paymentService = $paymentService;
    }

    public function handle(Request $request)
    {
        Log::info('Webhook recibido', $request->all());

        $topic = $request->get('topic');
        $id = $request->get('id');

        if ($topic !== 'payment') {
            return response()->json(['message' => 'Evento no procesable'], 200);
        }

        $payment = $this->paymentService->getPayment($id);

        if (!$payment) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        $paymentId = $payment['id'];
        $externalReference = $payment['external_reference'];
        $status = $payment['status'] ?? 'unknown';

        Log::info('Datos del pago', [
            'payment_id' => $paymentId,
            'external_reference' => $externalReference,
            'status' => $status
        ]);

        $membresia = Membresia::find($externalReference);
        if (!$membresia) {
            Log::warning('No se encontró la membresía asociada', ['external_reference' => $externalReference]);
            return response()->json(['message' => 'Membresía no encontrada'], 404);
        }

        // Buscar pago existente por payment_id
        $pago = Pago::where('payment_id', $paymentId)->first();

        if ($pago) {
            // Si cambió el estado, actualizarlo
            if ($pago->status !== $status) {
                $pago->update(['status' => $status]);
                Log::info("Estado de pago actualizado", ['payment_id' => $paymentId, 'nuevo_estado' => $status]);

                if ($status === 'approved') {
                    $this->membershipService->activateMembership((int) $externalReference);
                }
            }
            return response()->json(['status' => 'updated']);
        }

        // Crear nuevo pago si no existe
        $pago = Pago::create([
            'membresia_id' => $membresia->id,
            'payment_id' => $paymentId,
            'fecha' => now(),
            'detalle' => $payment['description'] ?? 'Pago de membresía',
            'metodo_pago_id' => 1,
            'importe' => $payment['transaction_amount'] ?? 0,
            'status' => $status,
            'payment_method_id' => $payment['payment_method_id'] ?? null,
            'payment_type_id' => $payment['payment_type_id'] ?? null,
            'authorization_code' => $payment['authorization_code'] ?? null,
            'payer_email' => $payment['payer']['email'] ?? null,
            'installments' => $payment['installments'] ?? null,
            'date_approved' => $payment['date_approved'] ?? null,
        ]);

        Log::info('Pago registrado correctamente', ['pago' => $pago]);

        if ($status === 'approved') {
            $this->membershipService->activateMembership((int) $externalReference);
        }

        return response()->json(['message' => 'Webhook procesado'], 200);
    }
}
