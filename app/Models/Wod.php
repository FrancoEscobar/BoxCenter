<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'user_id',
        'tipo_entrenamiento_id',
        'duracion',
        'fecha_creacion'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'fecha_creacion' => 'date',
    ];

    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'wod_ejercicio')
            ->withPivot('orden', 'series', 'repeticiones', 'duracion')
            ->withTimestamps();
    }

    public function tipoEntrenamiento()
    {
        return $this->belongsTo(TipoEntrenamiento::class, 'tipo_entrenamiento_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
