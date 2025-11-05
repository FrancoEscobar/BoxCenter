@extends('layouts.clean')

@section('title', 'Pago de Membresía')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4">Confirmar Pago</h3>

        <p><strong>Entrenamiento:</strong> {{ $tipoEntrenamiento->nombre }}</p>
        <p><strong>Plan:</strong> {{ $plan->nombre }}</p>
        <p><strong>Precio:</strong> ${{ number_format($plan->precio, 2) }}</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="text-center mt-4">
            <form method="POST" action="{{ route('athlete.payment.process') }}">
                @csrf
                <button type="submit" class="btn btn-success btn-lg">Pagar ahora</button>
                <a href="{{ route('athlete.planselection') }}" class="btn btn-secondary btn-lg">Volver</a>
            </form>
        </div>
    </div>
</div>
@endsection
