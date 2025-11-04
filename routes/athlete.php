<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Athlete\DashboardController;
use App\Http\Controllers\Athlete\ClassController;
use App\Http\Controllers\Athlete\MembershipController;
use App\Http\Controllers\Athlete\PaymentController;

Route::middleware(['auth', 'verified', 'role:atleta'])
    ->prefix('athlete')
    ->name('athlete.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('classes', ClassController::class);

        Route::get('/planselection', function () {
            return view('athlete.planselection');
        })->name('planselection');

        Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
        Route::post('/payment/procesar', [PaymentController::class, 'procesarPago'])->name('payment.process');

        Route::get('/payment/success', function () {
            return view('athlete.payment-success');
        })->name('payment.success');
    });
