<?php
namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    public function createPreference(array $data): string;
}