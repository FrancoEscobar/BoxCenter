<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Membresia;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dni' => 'required|string|max:10|unique:users,dni',
            'telefono' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date|before:today',
        ]);

        $atletaRole = Role::where('nombre', 'atleta')->first();

        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'rol_id' => $atletaRole->id,
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        // Verificar si por alguna razón ya existe una membresía asociada al usuario
        $membresiaExistente = Membresia::where('usuario_id', $user->id)
            ->whereIn('estado', ['pendiente', 'activa'])
            ->first();  

        // Caso raro: si existe una membresía pendiente, redirigir al pago; si está activa, al dashboard
        if ($membresiaExistente) {
            return match($membresiaExistente->estado) {
                'pendiente' => redirect()->route('athlete.payment')
                    ->with('info', 'Se encontró una membresía pendiente. Podés continuar con el pago.')
                    ->with('membresia_id', $membresiaExistente->id),
                'activa' => redirect()->route('athlete.dashboard'),
                default => redirect()->route('athlete.planselection')
                    ->with('success', 'Registro completado. Seleccioná tu plan para continuar.')
            };
        }

        // Si todo OK, redirigir al selector de plan
        return redirect()->route('athlete.planselection')
            ->with('success', 'Registro completado. Seleccioná tu plan para continuar.');
    }
}