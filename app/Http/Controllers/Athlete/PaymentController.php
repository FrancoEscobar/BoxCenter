<?php
namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\MembershipService;
use App\Services\PaymentGateway\MercadoPagoService;

class PaymentController extends Controller
{
    protected $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
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
            $checkoutUrl = $paymentGateway->createPreference($preferenceData);
            return redirect($checkoutUrl);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            dd($e->getApiResponse()); // muestra la respuesta completa de Mercado Pago
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
