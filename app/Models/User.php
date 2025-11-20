<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Membresia;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido',
        'email',
        'password',
        'dni',
        'telefono',
        'fecha_nacimiento',
        'rol_id',
    ];

    // Relación con el roles
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // Relación con membresías
    public function membresias()
    {
        return $this->hasMany(Membresia::class, 'usuario_id');
    }

    // Función para redirigir al usuario según su rol
    public function redirectToDashboard(): string
    {   
        $this->load('role');

        // Si es administrador
        if ($this->role && $this->role->nombre === 'admin') {
            return route('admin.dashboard');
        }

        // Si es coach
        if ($this->role && $this->role->nombre === 'coach') {
            return route('coach.dashboard');
        }

        // Si es atleta → verificamos la membresía
        if ($this->role && $this->role->nombre === 'atleta') {
            $membresia = $this->membresias()->latest()->first();

            // Nunca tuvo membresía
            if (!$membresia) {
                return route('athlete.planselection');
            }

            // Pendiente de pago
            $pago = $membresia->pagos()->latest()->first();
            if ($pago && $pago->status === 'pending') {
                return route('athlete.payment.pending', ['payment_id' => $pago->payment_id]);
            }

            // Vencida (por estado o por fecha)
            if ($membresia->estado === 'vencida' || ($membresia->fecha_fin && $membresia->fecha_fin < now())) {
                return route('athlete.planselection');
            }

            // Activa
            if ($membresia->estado === 'activa') {
                return route('athlete.dashboard');
            }

            // Fallback
            return route('athlete.planselection');
        }
    
        // Por defecto, si no tiene rol reconocido
        return route('home');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wods()
    {
        return $this->hasMany(Wod::class, 'user_id');
    }
}
