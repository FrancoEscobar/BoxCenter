<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class EjercicioController extends Controller
{
    public function index()
    {
        $ejercicios = Ejercicio::paginate(15);
        return view('coach.ejercicios.index', compact('ejercicios'));
    }

    public function create()
    {
        return view('coach.ejercicios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        Ejercicio::create($request->only('nombre', 'descripcion'));

        return redirect()
            ->route('coach.ejercicios.index')
            ->with('success', 'Ejercicio creado correctamente');
    }

    public function edit(Ejercicio $ejercicio)
    {
        return view('coach.ejercicios.edit', compact('ejercicio'));
    }

    public function update(Request $request, Ejercicio $ejercicio)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $ejercicio->update($request->only('nombre', 'descripcion'));

        return redirect()
            ->route('coach.ejercicios.index')
            ->with('success', 'Ejercicio actualizado correctamente');
    }

    public function destroy(Ejercicio $ejercicio)
    {
        $ejercicio->delete();

        return redirect()
            ->route('coach.ejercicios.index')
            ->with('success', 'Ejercicio eliminado correctamente');
    }
}