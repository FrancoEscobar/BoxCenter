<?php

namespace App\Livewire\Wod;
    
use Livewire\Component;
use App\Models\Ejercicio;
use App\Models\Wod;

class WodManager extends Component
{   
    public $wod = null;
    public array $ejercicios = []; 
    public $listaEjercicios;
    public $isEditing = true;
    public string $nuevoNombre = '';
    public string $nuevaDescripcion = '';
    // Modos: 'crear' (nuevo), 'ver' (lectura), 'editar' (modificar existente)
    public string $modo = 'crear';
    
    protected $rules = [
        'nuevoNombre' => 'required|string|max:100',
        'nuevaDescripcion' => 'nullable|string',
    ];

    public function mount(Wod $wod = null, $isEditing = true)
    {
        $this->listaEjercicios = Ejercicio::all();
        $this->isEditing = $isEditing;

        if ($wod && $wod->exists) {
            // MODO VER / EDITAR
            $this->wod = $wod;
            $this->modo = $isEditing ? 'editar' : 'ver';
            $this->cargarEjerciciosDelWod();
        } else {
            // MODO CREAR
            $this->modo = 'crear';
            $this->inicializarFilaVacia();
        }
    }

    public function cargarEjerciciosDelWod()
    {
        $this->ejercicios = [];
        foreach ($this->wod->ejercicios as $e) {
            $this->ejercicios[] = [
                'id' => $e->id,
                'orden' => $e->pivot->orden ?? 0,
                'series' => $e->pivot->series,
                'repeticiones' => $e->pivot->repeticiones,
                'duracion' => $e->pivot->duracion,
            ];
        }
    }

    public function inicializarFilaVacia()
    {
        $this->ejercicios = [
            [
                'id' => null,
                'orden' => 1,
                'series' => null,
                'repeticiones' => null,
                'duracion' => null,
            ]
        ];
    }

    public function activarEdicion()
    {
        $this->modo = 'editar';
    }

    public function cancelarEdicion()
    {
        $this->cargarEjerciciosDelWod();
        $this->modo = 'ver';
    }

    public function agregarEjercicio()
    {
        if ($this->modo === 'ver') return;

        $this->ejercicios[] = [
            'id' => null,
            'orden' => count($this->ejercicios) + 1,
            'series' => null,
            'repeticiones' => null,
            'duracion' => null,
        ];
    }

    public function eliminarEjercicio($index)
    {   
        if ($this->modo === 'ver') return;

        unset($this->ejercicios[$index]);
        $this->ejercicios = array_values($this->ejercicios);

        // Reordenar
        foreach ($this->ejercicios as $i => $e) {
            $this->ejercicios[$i]['orden'] = $i + 1;
        }
    }

    public function openModalCrear()
    {   
        logger('🔔 Livewire lanzó el evento show-bs-modal');
        $this->reset(['nuevoNombre', 'nuevaDescripcion']);
        $this->resetErrorBag();
        $this->dispatch('show-bs-modal');
    }

    public function resetModalState()
    {
        $this->reset(['nuevoNombre', 'nuevaDescripcion']);
        $this->resetErrorBag();
    }

    public function guardarEjercicio()
    {
        $this->validate();

        $nuevo = Ejercicio::create([
            'nombre' => $this->nuevoNombre,
            'descripcion' => $this->nuevaDescripcion,
        ]);

        $this->listaEjercicios = Ejercicio::all();

        $this->reset(['nuevoNombre', 'nuevaDescripcion']);

        $this->dispatch('hide-bs-modal');
    }

    public function render()
    {
        return view('livewire.wod.wod-manager');
    }
}
