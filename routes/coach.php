<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Coach\DashboardController;
use App\Http\Controllers\Coach\TrainingClassController;
use App\Http\Controllers\Coach\WodController;
use App\Http\Controllers\Coach\ClassCalendarController;
use App\Http\Controllers\Coach\HistoryController;
use App\Http\Controllers\Coach\EjercicioController;

Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [ClassCalendarController::class, 'index'])->name('calendar');
        Route::get('/history', [HistoryController::class, 'index'])->name('history');
        Route::get('/classes/{id}', [TrainingClassController::class, 'show'])->name('classes.show');

        Route::get('/wods', [WodController::class, 'index'])->name('wods.index');
        Route::get('/wods/create', [WodController::class, 'create'])->name('wods.create');
        Route::post('/wods', [WodController::class, 'store'])->name('wods.store');
        Route::get('/wods/{wod}/edit', [WodController::class, 'edit'])->name('wods.edit');
        Route::put('/wods/{wod}', [WodController::class, 'update'])->name('wods.update');
        Route::delete('/wods/{wod}', [WodController::class, 'destroy'])->name('wods.destroy');
    });
