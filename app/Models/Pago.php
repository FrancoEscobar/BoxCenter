<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'membresia_id',
        'payment_id',
        'fecha',
        'detalle',
        'metodo_pago_id',
        'importe',
        'status',
        'payment_method_id',
        'payment_type_id',
        'authorization_code',
        'payer_email',
        'installments',
        'date_approved',
    ];

    protected $casts = [
        'fecha' => 'date',
        'date_approved' => 'datetime',
        'importe' => 'decimal:2',
    ];

    // Relacion con Membresia
    public function membresia()
    {
        return $this->belongsTo(Membresia::class);
    }

    // Relacion con MetodoPago
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}