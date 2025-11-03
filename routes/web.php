<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Livewire\MembershipSelector;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/membresias', function () {
        return view('athlete.memberships');
    })->name('athlete.memberships');
});