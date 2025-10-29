<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas Breeze
require __DIR__.'/auth.php';

// Rutas por rol
require __DIR__.'/admin.php';
require __DIR__.'/coach.php';
require __DIR__.'/athlete.php';

Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = Auth::user();

    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'coach':
            return redirect()->route('coach.dashboard');
        default:
            return redirect()->route('athlete.dashboard');
    }
})->name('dashboard');