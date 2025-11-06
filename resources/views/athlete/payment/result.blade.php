<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado del pago</title>
</head>
<body style="font-family: sans-serif; text-align:center; margin-top:50px;">
    <h2>{{ $estado }}</h2>

    @isset($payment_id)
        <p>ID de pago: {{ $payment_id }}</p>
    @endisset

    <a href="{{ route('athlete.dashboard') }}">Volver al dashboard</a>
</body>
</html>
