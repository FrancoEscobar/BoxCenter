@extends('layouts.dashboard')

@section('title', 'Panel de Administración')

@section('dashboard-content')
<div class="row">
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.users.index') }}" class="card text-center p-4 shadow-sm text-decoration-none text-dark">
            <h5>Usuarios</h5>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.memberships.index') }}" class="card text-center p-4 shadow-sm text-decoration-none text-dark">
            <h5>Membresías</h5>
        </a>
    </div>
    <div class="col-md-4 mb-3">
        <a href="{{ route('admin.payments.index') }}" class="card text-center p-4 shadow-sm text-decoration-none text-dark">
            <h5>Pagos</h5>
        </a>
    </div>
</div>
@endsection
