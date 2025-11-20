<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Wod;
use App\Models\TipoEntrenamiento;
use Illuminate\Http\Request;

class WodController extends Controller
{
    public function index()
    {
        $wods = Wod::with(['tipoEntrenamiento', 'user'])->latest()->paginate(10);
        $tipos = TipoEntrenamiento::all();
        
        return view('coach.wods.index', compact('wods', 'tipos'));
    }

    public function create()
    {
        $tipos = TipoEntrenamiento::all();
        return view('coach.wods.form', compact('tipos'));
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

    public function show(Wod $wod)
    {
        $tipos = TipoEntrenamiento::all();

        return view('coach.wods.form', [
            'wod' => $wod,
            'tipos' => $tipos,
            'readonly' => true
        ]);
    }

    public function edit(Wod $wod)
    {
        $this->authorizeWod($wod);

        $tipos = TipoEntrenamiento::all();
        
        return view('coach.wods.form', compact('wod', 'tipos'));
    }

    public function update(Request $request, Wod $wod)
    {
        $this->authorizeWod($wod);

        $data = $request->validate([
            'nombre' => 'required|max:100',
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

        $wod->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'tipo_entrenamiento_id' => $data['tipo_entrenamiento_id'],
            'duracion' => $data['duracion'] ?? null,
        ]);

        $syncData = [];
        
        $wod->ejercicios()->detach();

        foreach ($data['ejercicios'] as $ej) {
            $wod->ejercicios()->attach($ej['id'], [
                'orden' => $ej['orden'],
                'series' => $ej['series'] ?? null,
                'repeticiones' => $ej['repeticiones'] ?? null,
                'duracion' => $ej['duracion'] ?? null,
            ]);
        }

        return redirect()->route('coach.wods.index')->with('success', 'WOD actualizado correctamente.');
    }

public function destroy(Wod $wod)
    {
        $this->authorizeWod($wod);
        $wod->ejercicios()->detach(); 
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
