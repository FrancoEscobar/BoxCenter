<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Membresia;
use App\Models\Plan;
use App\Models\TipoEntrenamiento;
use Illuminate\Support\Facades\Auth;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class PaymentController extends Controller
{
    public function index()
    {
        // Redirige directamente a crear la preferencia
        return redirect()->route('athlete.payment.create');
    }

    public function createPreference()
    {   
        try {
            // Obtener datos de sesión
            $planId = Session::get('plan_id');
            $tipoEntrenamientoId = Session::get('tipo_entrenamiento_id');
            $user = Auth::user();

            // Validaciones básicas
            if (!$planId || !$tipoEntrenamientoId) {
                return redirect()->route('athlete.planselection')
                    ->with('error', 'Debés seleccionar un plan antes de continuar al pago.');
            }

            $plan = Plan::find($planId);
            if (!$plan) {
                return redirect()->route('athlete.planselection')
                    ->with('error', 'El plan seleccionado no existe.');
            }

            // Crear o recuperar membresía pendiente
            $membresia = Membresia::firstOrCreate(
                [
                    'usuario_id' => $user->id,
                    'plan_id' => $planId,
                    'tipo_entrenamiento_id' => $tipoEntrenamientoId,
                    'estado' => 'pago_pendiente',
                ],
                [
                    'precio' => $plan->precio ?? 0,
                ]
            );

            Session::put('membresia_id', $membresia->id);

            // Configurar el token de acceso de prueba
            MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

            // Crear preferencia de pago
            $client = new PreferenceClient();

            $preferenceData = [
                "items" => [
                    [
                        "title" => "Membresía BoxCenter - " . $plan->nombre,
                        "quantity" => 1,
                        "currency_id" => "ARS",
                        "unit_price" => (float) $plan->precio,
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

            // Crear la preferencia en Mercado Pago
            $preference = $client->create($preferenceData);

            // Redirigir directamente al Checkout Pro
            return redirect($preference->init_point);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            dd('MercadoPago Error:', $e->getApiResponse());
        } catch (\Exception $e) {
            dd('Error General:', $e->getMessage());
        }
    }
}
