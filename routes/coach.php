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

        // Dashboard y generales
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [ClassCalendarController::class, 'index'])->name('calendar');
        Route::get('/history', [HistoryController::class, 'index'])->name('history');

        // Clases
        Route::get('/classes/{id}', [TrainingClassController::class, 'show'])->name('classes.show');

        // --- RUTAS DE WODS
        // 1. Listado
        Route::get('/wods', [WodController::class, 'index'])->name('wods.index');
        // 2. Crear y guardar
        Route::get('/wods/create', [WodController::class, 'create'])->name('wods.create');
        Route::post('/wods', [WodController::class, 'store'])->name('wods.store');
        // 3. Editar, actualizar, ver y eliminar    
        Route::get('/wods/{wod}/edit', [WodController::class, 'edit'])
            ->name('wods.edit')
            ->whereNumber('wod');
        Route::get('/wods/{wod}', [WodController::class, 'show'])
            ->name('wods.show')
            ->whereNumber('wod');
        Route::put('/wods/{wod}', [WodController::class, 'update'])
            ->name('wods.update')
            ->whereNumber('wod');
        Route::delete('/wods/{wod}', [WodController::class, 'destroy'])
            ->name('wods.destroy')
            ->whereNumber('wod');
    });
