<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function wods()
    {
        return $this->belongsToMany(Wod::class, 'wod_ejercicio')
            ->withPivot('orden', 'series', 'repeticiones', 'duracion')
            ->withTimestamps();
    }
}
