<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Livewire\MembershipSelector;
use App\Http\Controllers\Athlete\MercadoPagoWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

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

    switch ($user->role->nombre) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'coach':
            return redirect()->route('coach.dashboard');
        default:
            return redirect()->route('athlete.dashboard');
    }
})->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');

// Webhook de Mercado Pago sin CSRF
Route::withoutMiddleware([VerifyCsrfToken::class])
    ->post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago');

// Ruta para verificar el estado del pago
Route::get('/athlete/payment/status/{payment_id}', [\App\Http\Controllers\Athlete\PaymentController::class, 'status'])
    ->name('athlete.payment.status');