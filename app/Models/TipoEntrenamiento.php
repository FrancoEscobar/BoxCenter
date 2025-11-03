<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'tipos_entrenamiento';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];
}
