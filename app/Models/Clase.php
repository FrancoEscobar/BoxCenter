<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    protected $table = 'clases';

    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'tipo_entrenamiento_id',
        'coach_id',
        'wod_id',
        'estado',
        'cupo',
    ];

    protected $casts = [
        'fecha' => 'datetime:Y-m-d',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function tipo_entrenamiento()
    {
        return $this->belongsTo(TipoEntrenamiento::class, 'tipo_entrenamiento_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function wod()
    {
        return $this->belongsTo(Wod::class, 'wod_id');
    }
}
