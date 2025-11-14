<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Coach\DashboardController;
use App\Http\Controllers\Coach\TrainingClassController;
use App\Http\Controllers\Coach\WodController;
use App\Http\Controllers\Coach\ClassCalendarController;
use App\Http\Controllers\Coach\HistoryController;

Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [ClassCalendarController::class, 'index'])->name('calendar');
        Route::get('/history', [HistoryController::class, 'index'])->name('history');
        Route::get('/wods', [WodController::class, 'index'])->name('wods');
        Route::get('/classes/{id}', [TrainingClassController::class, 'show'])->name('classes.show');
    });
