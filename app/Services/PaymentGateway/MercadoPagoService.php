<?php
namespace App\Services\PaymentGateway;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 

class MercadoPagoService implements PaymentGatewayInterface
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
    }

    public function createPreference(array $data): string
    {
        $client = new PreferenceClient();

        $data['auto_return'] = $data['auto_return'] ?? 'approved';
        $data['notification_url'] = $data['notification_url'] ?? route('webhooks.mercadopago');

        $preference = $client->create($data);

        return $preference->init_point;
    }

    public function getPayment($paymentId)
    {
        \Log::info('Obteniendo pago desde Mercado Pago', ['id' => $paymentId]);

        $accessToken = MercadoPagoConfig::getAccessToken();

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if ($response->successful()) {
            \Log::info('Respuesta de Mercado Pago', $response->json());
            return $response->json();
        }

        \Log::warning('Fallo al obtener el pago', [
            'id' => $paymentId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }
}
