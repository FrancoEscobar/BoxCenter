@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>@yield('title')</h2>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger">Cerrar sesión</button>
    </form>
</div>

@yield('dashboard-content')
@endsection
