<?php
namespace App\Services\PaymentGateway;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService implements PaymentGatewayInterface
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
    }

    public function createPreference(array $data): string
    {
        $client = new PreferenceClient();
        $preference = $client->create($data);

        return $preference->init_point;
    }
}
