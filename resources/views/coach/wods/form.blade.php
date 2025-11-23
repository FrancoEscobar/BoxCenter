@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- 1. ENCABEZADO DINÁMICO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            @if(isset($readonly) && $readonly)
                Ver WOD
            @elseif(isset($wod))
                Editar WOD
            @else
                Crear WOD
            @endif
        </h2>
        
        {{-- Botón para pasar de VER a EDITAR --}}
        @if(isset($readonly) && $readonly)
            <a href="{{ route('coach.wods.edit', $wod) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Detalles
            </a>
        @endif
    </div>

    @php
        $action = isset($wod) ? route('coach.wods.update', $wod) : route('coach.wods.store');
        $isReadonly = isset($readonly) && $readonly;
    @endphp

    <!-- 2. FORMULARIO PRINCIPAL -->
    <form action="{{ $action }}" method="POST" id="formWod" onsubmit="return confirm('{{ isset($wod) ? '¿Guardar cambios?' : '¿Crear este WOD?' }}')">
        @csrf

        @if(isset($wod))
            @method('PUT')
        @endif

        <!-- Nombre -->
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" 
            value="{{ old('nombre', $wod->nombre ?? '') }}"
            required
            {{ $isReadonly ? 'disabled' : '' }}
            oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
            oninput="this.setCustomValidity('')">
        </div>

        <!-- Descripción -->
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" 
            required
            {{ $isReadonly ? 'disabled' : '' }} 
            oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
            oninput="this.setCustomValidity('')">{{ old('descripcion', $wod->descripcion ?? '') }}</textarea>
        </div>

        <!-- Tipo de Entrenamiento -->
        <div class="mb-3">
            <label class="form-label">Tipo de entrenamiento</label>
            <select name="tipo_entrenamiento_id" class="form-select" 
            required
            {{ $isReadonly ? 'disabled' : '' }} 
            oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
            oninput="this.setCustomValidity('')">
            <option value="" disabled {{ !isset($wod) ? 'selected' : '' }}>Seleccionar...</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Duración -->
        <div class="mb-3">
            <label class="form-label">Duración total (min)</label>
            <input type="number" name="duracion" class="form-control" min="0"
            value="{{ old('duracion', $wod->duracion ?? '') }}"
            required
            {{ $isReadonly ? 'disabled' : '' }} 
            onkeydown="if(event.key === '-' || event.key === '+') event.preventDefault();" 
            oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
            oninput="this.setCustomValidity('')">
        </div>

        <hr>

        <!-- 3. INTEGRACIÓN LIVEWIRE (Ejercicios) -->
        <h4>Ejercicios del WOD</h4>
        <div id="ejerciciosContainer">
            <livewire:wod.wod-manager 
                :wod="$wod ?? null"
                :is-editing="!($readonly ?? false)"
            />
        </div>

        <hr>

        <!-- 4. BOTONES DE ACCIÓN -->
        @if(!$isReadonly)
            <button type="submit" class="btn btn-primary mt-3">
                {{ isset($wod) ? 'Guardar cambios' : 'Crear WOD' }}
            </button>
            
            <a href="{{ route('coach.wods.index') }}" class="btn btn-secondary mt-3 ms-2">Cancelar</a>
        @else
            <a href="{{ route('coach.wods.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
        @endif
    </form>

</div>
@endsection