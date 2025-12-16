@extends('layouts.clean')

@section('title', 'Pago exitoso')

@section('content')
<div class="container my-3 my-md-5 px-2">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow-sm p-3 p-md-5 text-center">
                <h2 class="mb-3 text-success fw-bold fs-3 fs-md-2">¡Pago aprobado!</h2>
                <p class="text-muted mb-4 small">
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

                <div class="d-grid gap-2 mx-auto" style="max-width: 300px;">
                    <a href="{{ $dashboardRoute }}" class="btn btn-success btn-lg">
                        Comenzar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
