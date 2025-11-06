<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago de prueba - BoxCenter</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body style="font-family: sans-serif; text-align:center; margin-top:50px;">
    <h2>Pago de prueba con Mercado Pago</h2>
    <p>Plan: <strong>{{ $plan->nombre }}</strong></p>
    <p>Importe: <strong>${{ number_format($plan->precio, 2, ',', '.') }}</strong></p>

    <div id="wallet_container"></div>

    <script>
        const mp = new MercadoPago("{{ config('services.mercadopago.public_key') }}", {
            locale: 'es-AR'
        });

        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: "{{ $preference->id }}",
            },
            customization: {
                texts: {
                    valueProp: 'smart_option',
                },
            },
        });
    </script>
</body>
</html>
