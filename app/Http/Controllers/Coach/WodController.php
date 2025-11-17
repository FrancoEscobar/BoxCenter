<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Wod;
use App\Models\TipoEntrenamiento;
use Illuminate\Http\Request;
use App\Models\Ejercicio;

class WodController extends Controller
{
    public function index()
    {
        $wods = Wod::with(['tipoEntrenamiento', 'user'])->paginate(10);
        $tipos = TipoEntrenamiento::all();
        $ejercicios = Ejercicio::all();

        return view('coach.wods.index', compact('wods', 'tipos', 'ejercicios'));
    }

    public function create()
    {
        $tipos = TipoEntrenamiento::all();
        $ejercicios = Ejercicio::all();

        return view('coach.wods.create', compact('tipos', 'ejercicios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',
            'duracion' => 'nullable|integer',
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*.id' => 'required|exists:ejercicios,id',
            'ejercicios.*.orden' => 'required|integer',
            'ejercicios.*.series' => 'nullable|integer|min:1',
            'ejercicios.*.repeticiones' => 'nullable|integer|min:1',
            'ejercicios.*.duracion' => 'nullable|integer|min:0',
        ]);

        $wod = Wod::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'user_id' => auth()->id(),
            'tipo_entrenamiento_id' => $data['tipo_entrenamiento_id'],
            'duracion' => $data['duracion'] ?? null,
            'fecha_creacion' => now()->toDateString(),
        ]);

        foreach ($data['ejercicios'] as $ej) {
            $wod->ejercicios()->attach($ej['id'], [
                'orden' => $ej['orden'],
                'series' => $ej['series'] ?? null,
                'repeticiones' => $ej['repeticiones'] ?? null,
                'duracion' => $ej['duracion'] ?? null,
            ]);
        }

        return redirect()->route('coach.wods.index')
            ->with('success', 'WOD creado correctamente');
    }

    public function edit(Wod $wod)
    {
        $this->authorizeWod($wod);
        
        $tipos = TipoEntrenamiento::all();
        $ejercicios = Ejercicio::all();
        $relaciones = $wod->ejercicios()->orderBy('pivot_orden')->get();

        return view('coach.wods.edit', compact('wod', 'tipos', 'ejercicios', 'relaciones'));
    }

    public function update(Request $request, Wod $wod)
    {
        $this->authorizeWod($wod);

        $request->validate([
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable|string',
            'tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',
            'duracion' => 'nullable|integer',
            'fecha_creacion' => 'nullable|date',
        ]);

        $wod->update($request->all());

        return redirect()->route('coach.wods.index')->with('success', 'WOD actualizado.');
    }

    public function destroy(Wod $wod)
    {
        $this->authorizeWod($wod);

        $wod->delete();

        return redirect()->route('coach.wods.index')->with('success', 'WOD eliminado.');
    }

    private function authorizeWod(Wod $wod)
    {
        if ($wod->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar este WOD');
        }
    }
}
