@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">{{ $clase->nombre ?? 'Clase sin nombre' }}</h2>

    <ul class="list-group">
        <li class="list-group-item"><strong>ID:</strong> {{ $clase->id }}</li>
        <li class="list-group-item"><strong>Fecha:</strong> {{ $clase->fecha ?? 'No definida' }}</li>
        <li class="list-group-item"><strong>Coach:</strong> {{ $clase->coach->nombre ?? 'Sin asignar' }}</li>
    </ul>

    <a href="{{ route('coach.calendar') }}" class="btn btn-primary mt-3">Volver al calendario</a>
</div>
@endsection
