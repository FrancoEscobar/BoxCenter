@extends('layouts.clean')

@section('title', 'Pago en revisión')

@section('content')
<div class="container my-3 my-md-5 px-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm p-3 p-md-5 text-center">
                <h2 class="mb-3 text-warning fw-bold fs-4 fs-md-2">Pago pendiente</h2>
                <p class="text-muted mb-4 small">
                    Tu pago está siendo procesado por Mercado Pago. Esto puede tardar unos minutos. Una vez aprobado, tendrás acceso completo a tu membresía.
                </p>

                <div class="d-grid gap-2 mx-auto" style="max-width: 300px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#asistenciaModal">
                        Necesito ayuda
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de asistencia -->
<div class="modal fade" id="asistenciaModal" tabindex="-1" aria-labelledby="asistenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-2 mx-md-auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asistenciaModalLabel">Asistencia con el pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-start">
                <p>Si tu pago está pendiente en Mercado Pago, podés seguir estos pasos:</p>
                <ul>
                    <li>Verificá en tu cuenta de Mercado Pago que la transacción se esté procesando.</li>
                    <li>Algunos métodos de pago pueden tardar hasta 24 horas en confirmarse.</li>
                    <li>Si el pago no se aprueba en un tiempo razonable, intentá realizar la operación nuevamente o contactá a soporte.</li>
                </ul>
                <p class="mt-3">Para asistencia directa, contactanos:</p>
                <div class="p-3 bg-light rounded">
                    <p class="mb-1"><strong>Email:</strong> soporte@boxcenter.com</p>
                    <p class="mb-0"><strong>WhatsApp:</strong> +54 11 5555-5555</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ID del pago que se está verificando
    const paymentId = "{{ $payment_id }}"; // Debe pasarse desde el controlador al renderizar la vista
    const checkInterval = 10000; // 10 segundos

    async function checkPaymentStatus() {
        try {
            const res = await fetch(`/athlete/payment/status/${paymentId}`);
            if (!res.ok) return;
            const data = await res.json();

            if (data.status === 'approved') {
                window.location.href = "{{ route('athlete.payment.success') }}";
            } else if (data.status === 'rejected') {
                window.location.href = "{{ route('athlete.payment.failure') }}";
            }
            // si sigue pendiente, no hace nada, se chequea en el próximo intervalo
        } catch (err) {
            console.error('Error verificando estado del pago:', err);
        }
    }

    // Revisar cada X segundos
    setInterval(checkPaymentStatus, checkInterval);
</script>
@endsection
