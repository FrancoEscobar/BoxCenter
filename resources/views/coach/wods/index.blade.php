@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">WODs</h2>
        <a href="{{ route('coach.wods.create') }}" class="btn btn-primary">
            + Crear WOD
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($wods->isEmpty())
        <div class="alert alert-info">
            No hay WODs para mostrar.
        </div>
    @else
        <table class="table table-bordered table-striped align-middle table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Duración</th>
                    <th>Fecha creación</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wods as $wod)
                    {{-- 1. El onclick de la fila lleva a la vista SHOW (que ahora es el formulario readonly) --}}
                    <tr class="wod-row" style="cursor: pointer;" onclick="window.location='{{ route('coach.wods.show', $wod) }}'">
                        <td>{{ $wod->nombre }}</td>
                        <td>{{ $wod->tipoEntrenamiento->nombre }}</td>
                        <td>{{ $wod->duracion ? $wod->duracion . ' min' : '-' }}</td>
                        <td>{{ optional($wod->fecha_creacion)->format('d/m/Y') ?? '-' }}</td>

                        {{-- 2. IMPORTANTE: event.stopPropagation() en esta celda --}}
                        <td class="text-end" onclick="event.stopPropagation()">

                            {{-- Puedes descomentar esto si quieres un acceso directo a editar --}}
                            {{-- 
                            <a href="{{ route('coach.wods.edit', $wod) }}" class="btn btn-sm btn-warning me-1">
                                <i class="bi bi-pencil"></i>
                            </a> 
                            --}}

                            <form action="{{ route('coach.wods.destroy', $wod) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este WOD?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $wods->links() }}
        </div>
    @endif
</div>

<style>
    .wod-row {
        transition: all 0.2s ease;
    }

    .wod-row:hover td {
        background-color: #e9ecef;
    }
    
    .wod-row:hover .btn-danger {
        border-color: white;
    }
</style>
@endsection