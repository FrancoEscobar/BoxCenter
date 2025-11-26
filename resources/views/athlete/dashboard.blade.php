@extends('layouts.app')

@section('content')
{{-- Banner de próxima clase (componente Livewire) --}}
<livewire:athlete.next-class-banner/>

<div>
    {{-- Clases disponibles --}}
    <livewire:athlete.avaliable-classes/>
</div>

{{-- Estilos globales --}}
<style>
    .hover-shadow {
        transition: all 0.25s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .card:active {
        transform: scale(0.98);
        transition: 0.1s;
    }
</style>

@endsection

