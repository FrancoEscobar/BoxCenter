@extends('layouts.clean')

@section('title', 'Pago exitoso')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-5 text-center">
                <h2 class="mb-3 text-success fw-bold">¡Pago aprobado!</h2>
                <p class="text-muted mb-4">
                    Tu pago fue procesado correctamente. Ya podés comenzar a disfrutar de tu membresía en <strong>BoxCenter</strong>.
                </p>

                @php
                    $user = Auth::user();
                    switch ($user->role->name ?? '') {
                        case 'admin':
                            $dashboardRoute = route('admin.dashboard');
                            break;
                        case 'coach':
                            $dashboardRoute = route('coach.dashboard');
                            break;
                        case 'athlete':
                        default:
                            $dashboardRoute = route('athlete.dashboard');
                            break;
                    }
                @endphp

                <div class="d-grid gap-2 col-md-4 mx-auto">
                    <a href="{{ $dashboardRoute }}" class="btn btn-success btn-lg">
                        Comenzar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
