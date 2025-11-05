<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'usuario_id',
        'tipo_entrenamiento_id',
        'plan_id',
        'estado',
        'fecha_inicio',
        'fecha_vencimiento',
        'descuento',
        'importe',
    ];

        public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function tipoEntrenamiento()
    {
        return $this->belongsTo(TipoEntrenamiento::class);
    }
}
