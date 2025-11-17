@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">Crear WOD</h2>

    <form action="{{ route('coach.wods.store') }}" method="POST" id="formCrearWod" onsubmit="return confirm('¿Crear este WOD?')">
        @csrf

        <!-- Datos del WOD -->
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required oninvalid="this.setCustomValidity('Este campo es obligatorio')" oninput="this.setCustomValidity('')">
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" required oninvalid="this.setCustomValidity('Este campo es obligatorio')" oninput="this.setCustomValidity('')"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de entrenamiento</label>
            <select name="tipo_entrenamiento_id" class="form-select" required oninvalid="this.setCustomValidity('Este campo es obligatorio')" oninput="this.setCustomValidity('')">
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Duración total (min)</label>
            <input type="number" name="duracion" class="form-control" min="0" required onkeydown="if(event.key === '-' || event.key === '+') event.preventDefault();" oninvalid="this.setCustomValidity('Este campo es obligatorio')" oninput="this.setCustomValidity('')">
        </div>

        <hr>

        <!-- Ejercicios -->
        <h4>Ejercicios del WOD</h4>
        <div id="ejerciciosContainer">
            <livewire:wod.wod-ejercicios />
        </div>

        <hr>

        <button type="submit" class="btn btn-primary mt-3">Crear WOD</button>
    </form>

</div>
@endsection