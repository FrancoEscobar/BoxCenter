<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use Illuminate\Http\Request;

class TrainingClassController extends Controller
{
    public function show($id)
    {
        $clase = Clase::findOrFail($id);
        return view('coach.classes.show', compact('clase'));
    }
}