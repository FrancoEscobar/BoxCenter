<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function redirectToDashboard(): string
    {
        $this->load('role');
        return match($this->role->nombre ?? '') {
            'admin' => route('admin.dashboard'),
            'coach' => route('coach.dashboard'),
            'atleta' => route('athlete.dashboard'),
            default => route('home'),
        };
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
}
