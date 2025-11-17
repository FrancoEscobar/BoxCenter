<?php

namespace App\Livewire\Wod;

use Livewire\Component;
use App\Models\Ejercicio;

class WodEjercicios extends Component
{
    public array $ejercicios = []; 
    public $listaEjercicios;
    public string $nuevoNombre = '';
    public string $nuevaDescripcion = '';
    
    protected $rules = [
        'nuevoNombre' => 'required|string|max:100',
        'nuevaDescripcion' => 'nullable|string',
    ];

    public function mount()
    {
        $this->listaEjercicios = Ejercicio::all();

        // Iniciar con un ejercicio vacío
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

    public function agregarEjercicio()
    {
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
        unset($this->ejercicios[$index]);
        $this->ejercicios = array_values($this->ejercicios);

        // Reordenar
        foreach ($this->ejercicios as $i => $e) {
            $this->ejercicios[$i]['orden'] = $i + 1;
        }
    }

    public function openModalCrear()
    {
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
        return view('livewire.wod-ejercicios');
    }
}
