@extends('layouts.app')

@section('title', 'Pago de Membresía')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4">Confirmar Pago</h3>

        <p><strong>Entrenamiento:</strong> {{ $membresia->tipoEntrenamiento->nombre }}</p>
        <p><strong>Plan:</strong> {{ $membresia->plan->nombre }}</p>
        <p><strong>Precio:</strong> ${{ number_format($membresia->importe, 2) }}</p>

        <div class="text-center mt-4">
            <a href="#" class="btn btn-success btn-lg">Pagar ahora</a>
            <a href="{{ route('athlete.memberships') }}" class="btn btn-secondary btn-lg">Volver</a>
        </div>
    </div>
</div>
@endsection
