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

    private function updateMembershipStatus(Membresia $membresia, string $status)
    {
        switch ($status) {
            case 'approved':
                $this->membershipService->activateMembership($membresia->id);
                break;

            case 'pending':
                // Mantener como 'pago_pendiente' o actualizar timestamp
                $membresia->touch();
                break;

            case 'rejected':
                $membresia->update(['estado' => 'pago_rechazado']);
                break;

            case 'cancelled':
                $membresia->update(['estado' => 'pago_cancelado']);
                break;

            case 'failure':
                $membresia->update(['estado' => 'pago_fallido']);
                break;

            default:
                Log::warning('Estado de pago desconocido', ['status' => $status]);
                break;
        }
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

        Log::info('Estado de pago recibido desde Mercado Pago', [
            'payment_id' => $paymentId,
            'status' => $status
        ]);

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

        // Verificar si el pago ya existe
        $pago = Pago::where('payment_id', $paymentId)->first();

        if ($pago) {
            // Actualizar pago existente si el estado cambió
            if ($pago->status !== $status) {
                $pago->update(['status' => $status]);
                $this->updateMembershipStatus($membresia, $status);
            }
        } else {
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

            $this->updateMembershipStatus($membresia, $status);
        }

        Log::info('Pago registrado correctamente', ['pago' => $pago]);

        return response()->json(['message' => 'Webhook procesado'], 200);
    }
}
