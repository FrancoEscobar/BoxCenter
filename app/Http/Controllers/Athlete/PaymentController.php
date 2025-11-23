<?php
namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\MembershipService;
use App\Services\PaymentGateway\MercadoPagoService;

class PaymentController extends Controller
{
    protected $membershipService;
    protected $paymentGateway;

    public function __construct(MembershipService $membershipService, MercadoPagoService $paymentGateway)
    {
        $this->membershipService = $membershipService;
        $this->paymentGateway = $paymentGateway;
    }

    public function index()
    {
        return redirect()->route('athlete.payment.create');
    }

    public function createPreference()
    {       
        $planId = Session::get('plan_id');
        $tipoEntrenamientoId = Session::get('tipo_entrenamiento_id');

        if (!$planId || !$tipoEntrenamientoId) {
            return redirect()->route('athlete.planselection')
                ->with('error', 'Debés seleccionar un plan antes de continuar al pago.');
        }

        // Crear o recuperar membresía pendiente
        $membresia = $this->membershipService->createPendingMembership($planId, $tipoEntrenamientoId);
        Session::put('membresia_id', $membresia->id);

        // Inicializar el servicio de pago
        $paymentGateway = new MercadoPagoService();

        // Preparar datos para Mercado Pago
        $preferenceData = [
            "items" => [
                [
                    "title" => "Membresía BoxCenter - " . $membresia->plan->nombre,
                    "quantity" => 1,
                    "currency_id" => "ARS",
                    "unit_price" => round($membresia->plan->precio, 2),
                ],
            ],
            "external_reference" => $membresia->id,
            "back_urls" => [
                "success" => config('app.url') . "/athlete/payment/success",
                "failure" => config('app.url') . "/athlete/payment/failure",
                "pending" => config('app.url') . "/athlete/payment/pending",
            ],
            "auto_return" => "approved",
            "notification_url" => config('app.url') . "/webhooks/mercadopago",
        ];

        // Crear preferencia y redirigir
        try {
            $checkoutUrl = $this->paymentGateway->createPreference($preferenceData);
            return redirect($checkoutUrl);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            \Log::error('Error al crear preferencia', ['error' => $e->getMessage()]);
            return redirect()->route('athlete.planselection')
                ->with('error', 'Ocurrió un error al procesar el pago.');
        }
    }

    public function status($payment_id)
    {
        $pago = \App\Models\Pago::where('payment_id', $payment_id)->first();

        if (!$pago) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json(['status' => $pago->status]);
    }

    private function getPaymentForSession()
    {
        $membresiaId = Session::get('membresia_id');

        $pago = \App\Models\Pago::where('membresia_id', $membresiaId)
            ->latest()
            ->first();

        if (!$pago) {
            return null;
        }

        return $pago;
    }

    private function logPaymentState(?\App\Models\Pago $pago, ?\App\Models\Membresia $membresia, string $source)
    {
        if ($pago) {
            \Log::info("Estado de pago desde {$source}", [
                'payment_id' => $pago->payment_id,
                'status' => $pago->status,
                'membresia_id' => $membresia?->id
            ]);
        } else {
            \Log::info("No se encontró pago desde {$source}, la membresía será cancelada", [
                'membresia_id' => $membresia?->id
            ]); 
        }
    }

    private function handlePaymentView(string $view, string $source)
    {
        $pago = $this->getPaymentForSession();
        $membresiaId = Session::get('membresia_id');
        $membresia = \App\Models\Membresia::find($membresiaId);

        $this->logPaymentState($pago, $membresia, $source);

        if (!$pago && $membresia) {
            // Si no hay pago, eliminar membresía pendiente
            $membresia->delete();
            Session::forget('membresia_id');

            return redirect()->route('athlete.planselection')
                ->with('error', 'No se completó el pago.');
        }

        return view("athlete.payment.{$view}", [
            'payment_id' => $pago?->payment_id
        ]);
    }

    public function success()
    {
        return $this->handlePaymentView('success', 'success');
    }

    public function pending()
    {
        return $this->handlePaymentView('pending', 'pending');
    }

    public function failure()
    {
        return $this->handlePaymentView('failure', 'failure');
    }
}
