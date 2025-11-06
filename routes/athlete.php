<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Athlete\DashboardController;
use App\Http\Controllers\Athlete\ClassController;
use App\Http\Controllers\Athlete\MembershipController;
use App\Http\Controllers\Athlete\PaymentController;

// Rutas de resultado del pago (callbacks)
Route::get('/athlete/payment/success', [PaymentController::class, 'success'])->name('athlete.payment.success');
Route::get('/athlete/payment/failure', [PaymentController::class, 'failure'])->name('athlete.payment.failure');
Route::get('/athlete/payment/pending', [PaymentController::class, 'pending'])->name('athlete.payment.pending');

Route::middleware(['auth', 'verified', 'role:atleta'])
    ->prefix('athlete')
    ->name('athlete.')
    ->group(function () {

        // Rutas protegidas por la verificación de membresía activa
        Route::middleware(['active.membership'])->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::resource('classes', ClassController::class);
        });

        // Rutas accesibles sin membresía activa
        Route::get('/planselection', function () {
            return view('athlete.planselection');
        })->name('planselection');

        // Pagos con Mercado Pago (simulado)
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
        Route::get('/payment/create-preference', [PaymentController::class, 'createPreference'])->name('payment.create');
    });

